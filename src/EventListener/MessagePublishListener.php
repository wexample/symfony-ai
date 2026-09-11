<?php

namespace Wexample\SymfonyAi\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Wexample\SymfonyAi\Api\Normalizer\Entity\Message\DefaultMessageNormalizer;
use Wexample\SymfonyAi\Entity\Message;
use Wexample\SymfonyLive\Enum\LiveTopicAction;
use Wexample\SymfonyLive\Helper\LiveTopicHelper;
use Wexample\SymfonyLive\Service\LivePublisherService;

/**
 * Says on the session's topic that a turn was written, whoever wrote it.
 *
 * A message is published on its session and not on itself: a browser opens the
 * conversation before the next turn exists, so the only name it can subscribe to
 * is the one already there. Doctrine is listened to rather than the repository,
 * because a projection filling the thread from a transcript persists the rows
 * itself and would announce nothing.
 *
 * Publication waits for postFlush: a turn announced and then rolled back is one
 * no subscriber can un-hear.
 */
#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postFlush)]
class MessagePublishListener
{
    final public const EVENT_MESSAGE_CREATED = 'message-created';

    /** @var Message[] */
    private array $pending = [];

    public function __construct(
        private readonly LivePublisherService $publisher,
        private readonly DefaultMessageNormalizer $normalizer,
    ) {
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Message) {
            $this->pending[] = $entity;
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $pending = $this->pending;
        $this->pending = [];

        foreach ($pending as $message) {
            $this->publisher->publishEvent(
                LiveTopicHelper::entity($message->getSession(), LiveTopicAction::EVENT),
                self::EVENT_MESSAGE_CREATED,
                $this->normalizer->normalize($message)
            );
        }
    }
}
