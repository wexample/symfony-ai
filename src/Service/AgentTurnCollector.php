<?php

namespace Wexample\SymfonyAi\Service;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

/**
 * The answers to the calls an agent made in one turn, handed back to it
 * together: one sending, one agent reading them all.
 *
 * An agent may call several tools at once — four searches in a breath —, and
 * their answers arrive one by one, from the page or from the server. Each
 * sent on its own would open a turn of its own, and the conversation would go
 * on as four agents, each seeing one answer and answering the user on its
 * own. So an answer waits here until every call of its turn has one, and the
 * last one sends them all, in the order of the calls.
 *
 * What a turn is, which calls it holds and how its answers are sent is the
 * application's: it names the turn, lists the calls, and gives what to do
 * with the answers once complete. The page's answers and the server's may
 * come from two processes, so what waits and the lock taken to add to it are
 * kept under the project's `var/`, which both share.
 */
final class AgentTurnCollector
{
    /** How long answers wait for the rest of their turn. */
    private const int WAIT_SECONDS = 600;

    private readonly LockFactory $locks;

    public function __construct(
        #[Autowire(service: 'cache.app')]
        private readonly CacheItemPoolInterface $cache,
        #[Autowire(param: 'kernel.project_dir')]
        string $projectDir,
    ) {
        $directory = $projectDir.'/var/lock';

        if (! is_dir($directory)) {
            @mkdir($directory, 0777, true);
        }

        $this->locks = new LockFactory(new FlockStore($directory));
    }

    /**
     * Keeps the answer to a call, with what goes with it — what the user did
     * meanwhile —, and sends the turn's answers once all are in. Whether it
     * sent them.
     *
     * @param string $turn What names the turn: the request the calls belong to.
     * @param string[] $callIds Every call of the turn, in the order the agent made them.
     * @param string $callId The call this answer is for.
     * @param mixed $answer The answer, as it is sent.
     * @param array<int, mixed> $with What is sent before the answers.
     * @param callable(array<int, mixed> $messages): void $send Sends the turn's messages, the extras first.
     */
    public function collect(string $turn, array $callIds, string $callId, mixed $answer, array $with, callable $send): bool
    {
        $key = 'agent_turn_'.hash('xxh128', $turn);
        $lock = $this->locks->createLock($key, 30);
        $lock->acquire(true);

        try {
            $item = $this->cache->getItem($key);
            $state = $item->isHit() ? $item->get() : ['done' => false, 'answers' => [], 'with' => []];

            // A turn already sent, or a call answered twice — two pages —:
            // nothing to add, nothing to send again.
            if ($state['done'] || isset($state['answers'][$callId])) {
                return false;
            }

            $state['answers'][$callId] = $answer;
            $state['with'] = [...$state['with'], ...$with];
            $complete = count(array_intersect_key($state['answers'], array_flip($callIds))) >= count($callIds);

            $item->set($complete ? ['done' => true, 'answers' => [], 'with' => []] : $state);
            $item->expiresAfter(self::WAIT_SECONDS);
            $this->cache->save($item);
        } finally {
            $lock->release();
        }

        if (! $complete) {
            return false;
        }

        $messages = $state['with'];

        foreach ($callIds as $id) {
            if (array_key_exists($id, $state['answers'])) {
                $messages[] = $state['answers'][$id];
            }
        }

        $send($messages);

        return true;
    }
}
