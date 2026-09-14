<?php

namespace Wexample\SymfonyAi\Repository;

use Doctrine\ORM\QueryBuilder;
use Wexample\SymfonyAi\Entity\Agent;
use Wexample\SymfonyAi\Entity\Session;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\SessionEntityManipulatorTrait;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;

/**
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends AbstractRepository
{
    use SessionEntityManipulatorTrait;

    /**
     * A conversation read from the record sitting at that path.
     *
     * Nothing else is set: the record says what the session is, so whoever
     * wrote it hydrates the row from the file rather than from memory.
     */
    public function createNewSession(string $path): Session
    {
        return new Session($path);
    }

    /**
     * The conversations one agent held, the one last spoken to first.
     *
     * @return Session[]
     */
    public function findByAgent(Agent $agent): array
    {
        return $this->queryLastSpokenFirst()
            ->where('session.agent = :agent')
            ->setParameter('agent', $agent)
            ->getQuery()
            ->getResult();
    }

    /**
     * The conversations held inside one app, the one last spoken to first. What
     * ties a session to an app is the record it was read from, which lies inside
     * it.
     *
     * @return Session[]
     */
    public function findByPathPrefix(string $prefix): array
    {
        return $this->queryLastSpokenFirst()
            ->where('session.path LIKE :prefix')
            ->setParameter('prefix', addcslashes($prefix, '%_\\').'/%')
            ->getQuery()
            ->getResult();
    }

    /**
     * The conversations held under a qualified agent name, which is the only
     * handle on an agent a package ships: that one is code, so it has no record
     * to relate to.
     *
     * @return Session[]
     */
    public function findByAgentName(string $agentName): array
    {
        return $this->queryLastSpokenFirst()
            ->where('session.agentName = :agentName')
            ->setParameter('agentName', $agentName)
            ->getQuery()
            ->getResult();
    }

    /**
     * The conversations, the one last spoken to first.
     *
     * A conversation nobody ever answered has no last message, and falls back on
     * the moment it was opened — which is the last thing that happened to it.
     * Without that fallback those rows sort among themselves by nothing at all.
     */
    private function queryLastSpokenFirst(): QueryBuilder
    {
        // Selected to be ordered on, which is what DQL asks for, and hidden so
        // that what comes back is still a list of sessions.
        return $this->createQueryBuilder('session')
            ->addSelect('COALESCE(session.dateLastMessage, session.dateCreated) AS HIDDEN lastActivity')
            ->orderBy('lastActivity', self::SORT_DESC);
    }
}
