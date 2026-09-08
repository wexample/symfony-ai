<?php

namespace Wexample\SymfonyAi\Repository;

use Wexample\SymfonyAi\Entity\Model;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\ModelEntityManipulatorTrait;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;

/**
 * @method Model|null find($id, $lockMode = null, $lockVersion = null)
 * @method Model|null findOneBy(array $criteria, array $orderBy = null)
 * @method Model[]    findAll()
 * @method Model[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModelRepository extends AbstractRepository
{
    use ModelEntityManipulatorTrait;
}
