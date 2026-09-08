<?php

namespace Wexample\SymfonyAi\Repository;

use Wexample\SymfonyAi\Entity\Agent;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\AgentEntityManipulatorTrait;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;

/**
 * @method Agent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agent[]    findAll()
 * @method Agent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgentRepository extends AbstractRepository
{
    use AgentEntityManipulatorTrait;
}
