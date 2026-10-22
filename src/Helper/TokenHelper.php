<?php

namespace Wexample\SymfonyAi\Helper;

/**
 * How many tokens a text sent to a model weighs, as an order of size: about
 * four characters a token, which is what English prose and code average with
 * the tokenizers of today's models. Enough to see where a prompt weighs; not a
 * count — that is the model's tokenizer's, and only wex, which assembles the
 * prompt and knows the model, can ask it.
 */
class TokenHelper
{
    final public const int CHARACTERS_PER_TOKEN = 4;

    public static function estimate(string $text): int
    {
        return (int) ceil(mb_strlen($text) / self::CHARACTERS_PER_TOKEN);
    }
}
