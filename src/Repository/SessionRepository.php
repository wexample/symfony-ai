<?php

namespace Wexample\SymfonyAi\Repository;

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
     * The conversations one agent held, the one last spoken to first.
     *
     * @return Session[]
     */
    public function findByAgent(Agent $agent): array
    {
        return $this->findBy(
            ['agent' => $agent],
            ['dateLastMessage' => self::SORT_DESC]
        );
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
        return $this->findBy(
            ['agentName' => $agentName],
            ['dateLastMessage' => self::SORT_DESC]
        );
    }
}
