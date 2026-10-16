<?php

namespace Wexample\SymfonyAi\Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Wexample\SymfonyAi\Helper\TokenHelper;

class TokenHelperTest extends TestCase
{
    public function testAboutFourCharactersMakeAToken(): void
    {
        $this->assertSame(0, TokenHelper::estimate(''));
        $this->assertSame(1, TokenHelper::estimate('abc'));
        $this->assertSame(2, TokenHelper::estimate('abcdefgh'));
        // Characters, not bytes: an accented text weighs what it reads.
        $this->assertSame(1, TokenHelper::estimate('éàçù'));
    }
}
