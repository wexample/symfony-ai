<?php

namespace Wexample\SymfonyAi\Entity\Traits\Manipulator;

use Wexample\SymfonyAi\Entity\Model;
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;

trait ModelEntityManipulatorTrait
{
    use EntityManipulatorTrait;

    public static function getEntityClassName(): string
    {
        return Model::class;
    }
}
