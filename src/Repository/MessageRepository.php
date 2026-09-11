<?php

namespace Wexample\SymfonyAi\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Wexample\SymfonyAi\Entity\Message;
use Wexample\SymfonyAi\Entity\Session;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\MessageEntityManipulatorTrait;
use Wexample\SymfonyAi\Event\MessageCreatedEvent;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Message      saveNewMessage(Session $session, string $type, string $body)
 */
class MessageRepository extends AbstractRepository
{
    use MessageEntityManipulatorTrait;

    public function __construct(
        ManagerRegistry $registry,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($registry);
    }

    public function createNewMessage(
        Session $session,
        string $type,
        string $body,
    ): Message {
        $message = new Message($session);
        $message->setType($type);
        $message->setBody($body);
        $message->setDateCreatedNow();

        return $message;
    }

    /**
     * Writes a turn, and says so for whoever knows how to answer it.
     *
     * The event goes out once the record is written and not before: what
     * listens to it reads the message back by its identity, from elsewhere.
     */
    public function saveNewMessageAndPushEvent(
        Session $session,
        string $type,
        string $body,
    ): Message {
        $message = $this->saveNewMessage(
            $session,
            $type,
            $body
        );

        $this->eventDispatcher->dispatch(
            new MessageCreatedEvent($message)
        );

        return $message;
    }

    /**
     * The conversation as it was held, oldest first.
     *
     * @return Message[]
     */
    public function findBySession(Session $session): array
    {
        return $this->findBy(
            ['session' => $session],
            ['dateCreated' => self::SORT_ASC]
        );
    }

    /**
     * The same conversation, for a reader taking it a page at a time.
     */
    public function queryBySessionOldestFirst(Session $session): QueryBuilder
    {
        return $this
            ->queryByField('session', $session)
            ->orderBy($this->getEntityQueryAlias().'.dateCreated', self::SORT_ASC);
    }

    /**
     * The row a provider's own identifier points at, which is what says a
     * transcript read twice must not be stored twice.
     */
    public function findOneByProviderMessageIdentifier(string $identifier): ?Message
    {
        return $this->findOneBy(['providerMessageIdentifier' => $identifier]);
    }
}
