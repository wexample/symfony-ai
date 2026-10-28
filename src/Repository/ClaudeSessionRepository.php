<?php

namespace Wexample\SymfonyAi\Repository;

use Symfony\Component\Uid\Uuid;
use Wexample\SymfonyAi\Entity\ClaudeSession;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\ClaudeSessionEntityManipulatorTrait;

/**
 * @method ClaudeSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClaudeSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClaudeSession[]    findAll()
 * @method ClaudeSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClaudeSessionRepository extends SessionRepository
{
    use ClaudeSessionEntityManipulatorTrait;

    /**
     * A conversation read from the record sitting at that path.
     *
     * Nothing else is set: the record says what the session is, so whoever
     * wrote it hydrates the row from the file rather than from memory.
     */
    public function createNewClaudeSession(string $path): ClaudeSession
    {
        return new ClaudeSession($path);
    }

    /**
     * The conversations held inside one app, the one last spoken to first. What
     * ties a session to an app is the record it was read from, which lies inside
     * it.
     *
     * @return ClaudeSession[]
     */
    public function findByPathPrefix(string $prefix): array
    {
        return $this->queryLastSpokenFirst()
            ->where($this->queryField('path').' LIKE :prefix')
            ->setParameter('prefix', addcslashes($prefix, '%_\\').'/%')
            ->getQuery()
            ->getResult();
    }

    /**
     * One conversation, on condition that it was held inside that directory.
     *
     * Asked for this way rather than by identity alone wherever the address
     * names both: another app's conversation answering there would be shown,
     * and spoken in, under a name that does not hold it.
     */
    public function findByPathPrefixAndId(
        string $prefix,
        Uuid $id,
    ): ?ClaudeSession {
        foreach ($this->findByPathPrefix($prefix) as $session) {
            if ($session->getId()->equals($id)) {
                return $session;
            }
        }

        return null;
    }
}
