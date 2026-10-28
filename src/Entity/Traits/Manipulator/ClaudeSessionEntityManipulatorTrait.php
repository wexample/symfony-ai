<?php

namespace Wexample\SymfonyAi\Entity\Traits\Manipulator;

use Wexample\SymfonyAi\Entity\ClaudeSession;
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;

trait ClaudeSessionEntityManipulatorTrait
{
    use EntityManipulatorTrait;

    public static function getEntityClassName(): string
    {
        return ClaudeSession::class;
    }
}
