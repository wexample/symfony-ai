<?php

namespace Wexample\SymfonyAi\Repository;

use Doctrine\ORM\QueryBuilder;
use Wexample\SymfonyAi\Entity\Agent;
use Wexample\SymfonyAi\Entity\Session;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\SessionEntityManipulatorTrait;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;

/**
 * The conversations of every engine: what is asked of a session as such.
 * What an engine alone can answer — a Claude session by the record it was
 * read from — is asked of the engine's own repository, built on this one.
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends AbstractRepository
{
    use SessionEntityManipulatorTrait;

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
    protected function queryLastSpokenFirst(): QueryBuilder
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
