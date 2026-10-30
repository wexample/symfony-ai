<?php

namespace Wexample\SymfonyAi\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Service\ResetInterface;
use Wexample\SymfonyAi\Api\Controller\Entity\MessageController;
use Wexample\SymfonyAi\Entity\Message;
use Wexample\SymfonyAi\Repository\MessageRepository;
use Wexample\SymfonyLoader\Service\AgentBackToolsService;

/**
 * The calls an agent makes to the tools of the server, answered here before
 * the page hears of them.
 *
 * Whatever writes an agent's call — an engine's callback, a transcript — writes
 * a tool message; when it calls a tool of the server (AgentBackToolsService),
 * the call is marked as answered here before it is saved, so the page shows it
 * and does not run it, and once saved it is run as the conversation's owner
 * and answered as the page answers: a tool message naming the call, which the
 * engine's listener sends on (MessageCreatedEvent).
 *
 * Run after the flush, as a request of its own would: a call announced and
 * then rolled back is not one to answer.
 */
#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::postFlush)]
class AgentBackToolListener implements ResetInterface
{
    /** @var Message[] */
    private array $pending = [];

    public function __construct(
        private readonly AgentBackToolsService $agentBackTools,
    ) {
    }

    public function reset(): void
    {
        $this->pending = [];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $message = $args->getObject();
        $call = $message instanceof Message ? self::readCall($message) : null;

        if (! $call || $this->agentBackTools->isAnsweredHere($call) || ! $this->agentBackTools->handles($call)) {
            return;
        }

        $message->setBody(json_encode($this->agentBackTools->markAnsweredHere($call), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->pending[] = $message;
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $pending = $this->pending;
        $this->pending = [];

        /** @var MessageRepository $repository */
        $repository = $args->getObjectManager()->getRepository(Message::class);

        foreach ($pending as $message) {
            $call = self::readCall($message);
            $callId = (string) $message->getId();
            $results = $this->agentBackTools->answer($call, self::ownerOf($message));

            $repository->saveNewMessageAndPushEvent(
                $message->getSession(),
                Message::TYPE_TOOL,
                json_encode(
                    ['tool' => $call['name'], 'callId' => $callId, 'count' => count($results), 'results' => $results],
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                ),
                MessageController::TOOL_RESULT_PREFIX.$callId,
            );
        }
    }

    /**
     * The call a tool message carries — `{name, arguments}` —, null for a
     * page's answer (`callId`) or anything else.
     *
     * @return array<string, mixed>|null
     */
    public static function readCall(Message $message): ?array
    {
        if (Message::TYPE_TOOL !== $message->getType()) {
            return null;
        }

        $body = json_decode((string) $message->getBody(), true);

        return is_array($body) && is_string($body['name'] ?? null) && ! isset($body['callId']) ? $body : null;
    }

    /**
     * Whose conversation it is, for a session that says — the tools answer
     * for that account. One that does not runs them as the request stands.
     */
    private static function ownerOf(Message $message): ?UserInterface
    {
        $session = $message->getSession();
        $owner = method_exists($session, 'getUser') ? $session->getUser() : null;

        return $owner instanceof UserInterface ? $owner : null;
    }
}
