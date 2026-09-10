<?php

namespace Wexample\SymfonyAi\Entity\Traits\Manipulator;

use Wexample\SymfonyAi\Entity\Session;
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;

trait SessionEntityManipulatorTrait
{
    use EntityManipulatorTrait;

    public static function getEntityClassName(): string
    {
        return Session::class;
    }
}
