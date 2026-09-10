<?php

namespace Wexample\SymfonyAi\Repository;

use Doctrine\ORM\QueryBuilder;
use Wexample\SymfonyAi\Entity\Message;
use Wexample\SymfonyAi\Entity\Session;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\MessageEntityManipulatorTrait;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends AbstractRepository
{
    use MessageEntityManipulatorTrait;

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
