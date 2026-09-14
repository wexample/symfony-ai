<?php

namespace Wexample\SymfonyAi\Repository;

use Symfony\Component\Uid\Uuid;
use Wexample\SymfonyAi\Entity\Model;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\ModelEntityManipulatorTrait;
use Wexample\SymfonyHelpers\Repository\AbstractRepository;

/**
 * @method Model|null find($id, $lockMode = null, $lockVersion = null)
 * @method Model|null findOneBy(array $criteria, array $orderBy = null)
 * @method Model[]    findAll()
 * @method Model[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Model       saveNewModel(Uuid $id)
 */
class ModelRepository extends AbstractRepository
{
    use ModelEntityManipulatorTrait;

    /**
     * A model under the identity the registry gave it.
     *
     * Nothing else is set: what the model is comes from the list it was read
     * from, so whoever read it fills the row from those values.
     */
    public function createNewModel(Uuid $id): Model
    {
        return new Model($id);
    }
}
