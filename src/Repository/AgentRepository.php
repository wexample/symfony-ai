<?php

namespace Wexample\SymfonyAi\Repository;

use Symfony\Component\Uid\Uuid;
use Wexample\SymfonyAi\Entity\Agent;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\AgentEntityManipulatorTrait;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;

/**
 * @method Agent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agent[]    findAll()
 * @method Agent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Agent       saveNewAgent(string $path)
 */
class AgentRepository extends AbstractRepository
{
    use AgentEntityManipulatorTrait;

    /**
     * An agent read from the declaration sitting at that path.
     *
     * Nothing else is set: the file owns what it says, so whoever read it
     * hydrates the row from those values rather than from memory.
     */
    public function createNewAgent(string $path): Agent
    {
        return new Agent($path);
    }

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

    /**
     * One agent, on condition that it is declared under that directory.
     *
     * Asked for this way rather than by identity alone wherever the address
     * names both: an agent of another app answering there would be an agent
     * shown, and acted upon, under a name that does not hold it.
     */
    public function findByPathPrefixAndId(
        string $prefix,
        Uuid $id,
    ): ?Agent {
        foreach ($this->findByPathPrefix($prefix) as $agent) {
            if ($agent->getId()->equals($id)) {
                return $agent;
            }
        }

        return null;
    }
}
