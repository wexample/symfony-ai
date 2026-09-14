<?php

namespace Wexample\SymfonyAi\Repository;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;
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
        return $this->queryByAgent($agent)
            ->getQuery()
            ->getResult();
    }

    /**
     * The same, left open: what a listing reads a page at a time.
     */
    public function queryByAgent(Agent $agent): QueryBuilder
    {
        return $this->queryLastSpokenFirst()
            ->where($this->queryField('agent').' = :agent')
            ->setParameter('agent', $agent);
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
    ): ?Session {
        foreach ($this->findByPathPrefix($prefix) as $session) {
            if ($session->getId()->equals($id)) {
                return $session;
            }
        }

        return null;
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
            ->where($this->queryField('agentName').' = :agentName')
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
        // The alias the helpers of the base repository expect: counting a
        // builder, which is what a paginated listing does first, goes through
        // it, and a hand-picked one would leave the count unable to name the
        // rows it is counting.
        $alias = $this->getEntityQueryAlias();

        // Selected to be ordered on, which is what DQL asks for, and hidden so
        // that what comes back is still a list of sessions.
        return $this->createQueryBuilder($alias)
            ->addSelect(
                sprintf('COALESCE(%s.dateLastMessage, %s.dateCreated) AS HIDDEN lastActivity', $alias, $alias)
            )
            ->orderBy('lastActivity', self::SORT_DESC);
    }
}
