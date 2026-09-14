<?php

namespace Wexample\SymfonyAi\Service;

use Generator;
use Wexample\SymfonyAi\Entity\Message;

/**
 * Turns a Claude transcript into the plain arrays a message is hydrated from.
 *
 * This is the only place the provider is named. Its transcript is one json
 * object per line, and most of them are its own bookkeeping — the title it
 * gives itself, the last prompt, queue operations, attachments — which say
 * nothing about the conversation and are left where they are.
 */
final readonly class ClaudeTranscriptReader
{
    /** The line types that carry something said. */
    public const CARRIED_TYPES = [
        Message::TYPE_ASSISTANT,
        Message::TYPE_SYSTEM,
        Message::TYPE_USER,
    ];

    /**
     * What a tool call is about, in the order the keys are looked for. Every
     * tool takes a different shape of input and the front renders no json, so
     * one telling value is what says what the call was for.
     */
    public const TOOL_SUBJECT_KEYS = [
        'file_path',
        'command',
        'pattern',
        'path',
        'url',
        'query',
    ];

    /**
     * Every message the transcript holds, in the order it was written.
     *
     * One line makes at most one message, since the identifier the provider
     * gives the line is what tells a transcript read twice from two. A line
     * that says nothing — a tool result, a thought, an empty turn — makes none.
     *
     * @return Generator<array<string, mixed>>
     */
    public function read(string $path): Generator
    {
        $handle = fopen($path, 'r');

        while (false !== $line = fgets($handle)) {
            $values = $this->message(json_decode($line, true));

            if (null !== $values) {
                yield $values;
            }
        }

        fclose($handle);
    }

    /**
     * @param array<string, mixed> $line
     *
     * @return array<string, mixed>|null
     */
    private function message(array $line): ?array
    {
        if (! in_array($line['type'] ?? null, self::CARRIED_TYPES, true)) {
            return null;
        }

        $body = $this->body($line);

        if ('' === $body) {
            return null;
        }

        return [
            MessageHydrator::KEY_TYPE => $line['type'],
            MessageHydrator::KEY_BODY => $body,
            MessageHydrator::KEY_DATE_CREATED => $line['timestamp'] ?? null,
            MessageHydrator::KEY_TOKENS => $this->tokens($line),
            MessageHydrator::KEY_PROVIDER_MESSAGE_IDENTIFIER => $line['uuid'] ?? null,
        ];
    }

    /**
     * What the line says, or an empty string when it says nothing.
     *
     * A line the engine wrote about itself rather than about the conversation —
     * a compaction boundary — carries its text at the top level instead of in a
     * message, which is why it is read before the blocks.
     *
     * @param array<string, mixed> $line
     */
    private function body(array $line): string
    {
        $content = $line['message']['content'] ?? $line['content'] ?? null;

        if (is_string($content)) {
            return trim($content);
        }

        if (! is_array($content)) {
            return '';
        }

        $parts = [];

        foreach ($content as $block) {
            $part = $this->block($block);

            if ('' !== $part) {
                $parts[] = $part;
            }
        }

        return implode("\n\n", $parts);
    }

    /**
     * One block of a message: what was written, or what was called.
     *
     * A thought and a tool result are dropped — the first is not addressed to
     * anyone, the second is the tool answering, which the call already announces.
     *
     * @param array<string, mixed> $block
     */
    private function block(array $block): string
    {
        if ('text' === ($block['type'] ?? null)) {
            return trim($block['text'] ?? '');
        }

        if ('tool_use' === ($block['type'] ?? null)) {
            return $this->toolCall($block);
        }

        return '';
    }

    /**
     * What a tool call says it is about, in one line.
     *
     * Public because a turn is also read as it happens, from events that carry
     * the same name and input: both readings must word the call the same way,
     * or the same call would be two lines in the thread.
     *
     * @param array<string, mixed> $block
     */
    public function toolCall(array $block): string
    {
        $name = $block['name'] ?? '';

        foreach (self::TOOL_SUBJECT_KEYS as $key) {
            $subject = $block['input'][$key] ?? null;

            if (is_string($subject) && '' !== $subject) {
                return trim($name.' '.$subject);
            }
        }

        return $name;
    }

    /**
     * What the turn cost, everything the provider counted for it: what was sent,
     * what it wrote back, and the cache it read or filled on the way.
     *
     * @param array<string, mixed> $line
     */
    private function tokens(array $line): ?int
    {
        $usage = $line['message']['usage'] ?? null;

        if (! is_array($usage)) {
            return null;
        }

        return (int) ($usage['input_tokens'] ?? 0)
            + (int) ($usage['output_tokens'] ?? 0)
            + (int) ($usage['cache_creation_input_tokens'] ?? 0)
            + (int) ($usage['cache_read_input_tokens'] ?? 0);
    }
}
