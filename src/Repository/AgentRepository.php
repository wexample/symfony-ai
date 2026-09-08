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

    /**
     * The agents declared under a directory, named after it.
     *
     * What that directory is stays the caller's business: an agent knows the
     * file it comes from, and whoever mounted that file knows what it means.
     *
     * @return Agent[]
     */
    public function findByPathPrefix(string $prefix): array
    {
        return $this->createQueryBuilder('agent')
            ->where('agent.path LIKE :prefix')
            ->setParameter('prefix', addcslashes($prefix, '%_\\').'/%')
            ->orderBy('agent.name', self::SORT_ASC)
            ->getQuery()
            ->getResult();
    }
}
