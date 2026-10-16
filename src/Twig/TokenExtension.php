<?php

namespace Wexample\SymfonyAi\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Wexample\SymfonyAi\Helper\TokenHelper;

/**
 * `text|ai_token_estimate`: the order of size of a text in tokens
 * (TokenHelper), for a page showing what a model is sent.
 */
class TokenExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('ai_token_estimate', TokenHelper::estimate(...)),
        ];
    }
}
