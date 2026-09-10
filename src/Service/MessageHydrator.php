<?php

namespace Wexample\SymfonyAi\Service;

use DateTime;
use Wexample\SymfonyAi\Entity\Message;

/**
 * Translates a message between a plain array and its record, both ways.
 *
 * The array is already neutral: turning a provider's own transcript into these
 * keys is the reader's job, and is the only place a provider is named.
 */
final readonly class MessageHydrator
{
    public const KEY_BODY = 'body';
    public const KEY_DATE_CREATED = 'date_created';
    public const KEY_PROVIDER_MESSAGE_IDENTIFIER = 'provider_message_identifier';
    public const KEY_TOKENS = 'tokens';
    public const KEY_TYPE = 'type';

    /**
     * @param array<string, mixed> $values
     */
    public function hydrate(
        Message $message,
        array $values
    ): Message {
        if (isset($values[self::KEY_TYPE])) {
            $message->setType($values[self::KEY_TYPE]);
        }

        if (isset($values[self::KEY_DATE_CREATED])) {
            $message->setDateCreated(new DateTime($values[self::KEY_DATE_CREATED]));
        }

        return $message
            ->setBody($values[self::KEY_BODY] ?? null)
            ->setTokens(
                isset($values[self::KEY_TOKENS])
                    ? (int) $values[self::KEY_TOKENS]
                    : null
            )
            ->setProviderMessageIdentifier(
                $values[self::KEY_PROVIDER_MESSAGE_IDENTIFIER] ?? null
            );
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(Message $message): array
    {
        return [
            self::KEY_TYPE => $message->getType(),
            self::KEY_BODY => $message->getBody(),
            self::KEY_DATE_CREATED => $message->getDateCreated()?->format(DATE_ATOM),
            self::KEY_TOKENS => $message->getTokens(),
            self::KEY_PROVIDER_MESSAGE_IDENTIFIER => $message->getProviderMessageIdentifier(),
        ];
    }
}
