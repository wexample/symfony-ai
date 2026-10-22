<?php

namespace Wexample\SymfonyAi\Traits;

use Wexample\SymfonyAi\WexampleSymfonyAiBundle;
use Wexample\SymfonyHelpers\Traits\BundleClassTrait;

trait SymfonyAiBundleClassTrait
{
    use BundleClassTrait;

    public static function getBundleClassName(): string
    {
        return WexampleSymfonyAiBundle::class;
    }
}
