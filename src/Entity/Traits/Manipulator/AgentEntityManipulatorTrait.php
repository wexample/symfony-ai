<?php

namespace Wexample\SymfonyAi\Entity\Traits\Manipulator;

use Wexample\SymfonyAi\Entity\Agent;
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;

trait AgentEntityManipulatorTrait
{
    use EntityManipulatorTrait;

    public static function getEntityClassName(): string
    {
        return Agent::class;
    }
}
