<?php

namespace Wexample\SymfonyAi\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Wexample\SymfonyAi\Service\AgentTurnCollector;

/**
 * The answers to the calls an agent made in one turn go back together, once
 * every call has one: one sending, not one per answer.
 */
class AgentTurnCollectorTest extends TestCase
{
    /** @var array<int, array<int, mixed>> */
    private array $sent = [];

    public function testTheTurnIsSentOnceEveryCallHasItsAnswerInTheOrderOfTheCalls(): void
    {
        $collector = $this->collector();
        $calls = ['a', 'b', 'c'];

        self::assertFalse($collector->collect('turn', $calls, 'c', 'third', [], $this->send(...)));
        self::assertFalse($collector->collect('turn', $calls, 'a', 'first', ['event'], $this->send(...)));
        self::assertSame([], $this->sent);

        self::assertTrue($collector->collect('turn', $calls, 'b', 'second', [], $this->send(...)));
        self::assertSame([['event', 'first', 'second', 'third']], $this->sent);
    }

    public function testACallAnsweredTwiceIsCountedOnceAndATurnSentIsNotSentAgain(): void
    {
        $collector = $this->collector();
        $calls = ['a', 'b'];

        self::assertFalse($collector->collect('turn', $calls, 'a', 'first', [], $this->send(...)));
        self::assertFalse($collector->collect('turn', $calls, 'a', 'first again', [], $this->send(...)));
        self::assertTrue($collector->collect('turn', $calls, 'b', 'second', [], $this->send(...)));
        self::assertFalse($collector->collect('turn', $calls, 'b', 'second again', [], $this->send(...)));
        self::assertCount(1, $this->sent);
    }

    public function testASingleCallIsSentAtOnceAndTurnsAreKeptApart(): void
    {
        $collector = $this->collector();

        self::assertTrue($collector->collect('one', ['a'], 'a', 'only', [], $this->send(...)));
        self::assertFalse($collector->collect('two', ['a', 'b'], 'a', 'half', [], $this->send(...)));
        self::assertCount(1, $this->sent);
    }

    private function collector(): AgentTurnCollector
    {
        return new AgentTurnCollector(new ArrayAdapter(), sys_get_temp_dir());
    }

    private function send(array $messages): void
    {
        $this->sent[] = $messages;
    }
}
