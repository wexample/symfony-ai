<?php

namespace Wexample\SymfonyAi\Entity\Traits\Manipulator;

use Wexample\SymfonyAi\Entity\Message;
use Wexample\SymfonyHelpers\Entity\Traits\Manipulator\EntityManipulatorTrait;

trait MessageEntityManipulatorTrait
{
    use EntityManipulatorTrait;

    public static function getEntityClassName(): string
    {
        return Message::class;
    }
}
