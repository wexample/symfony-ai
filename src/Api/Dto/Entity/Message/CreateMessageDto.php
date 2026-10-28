<?php

namespace Wexample\SymfonyAi\Api\Dto\Entity\Message;

use Symfony\Component\Validator\Constraints\Type;
use Wexample\SymfonyApi\Api\Attribute\RequiredDtoProperty;
use Wexample\SymfonyApi\Api\Dto\AbstractDto;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

/**
 * What has to be said to speak in a conversation: which one, and what.
 *
 * The type is deliberately not asked for. A turn written through this endpoint
 * was spoken by the operator, and letting the caller name its author would let
 * it write the answers too. The one other thing the page says is what came of
 * a tool the agent called in it (`toolResult`: the tool, the call it answers,
 * the results), which is a tool turn and not the operator's.
 */
class CreateMessageDto extends AbstractDto
{
    #[Type(VariableHelper::VARIABLE_TYPE_STRING)]
    #[RequiredDtoProperty]
    public string $session;

    #[Type(VariableHelper::VARIABLE_TYPE_STRING)]
    public ?string $body = null;

    /** @var array{tool: string, callId: string, results: array}|null */
    #[Type(VariableHelper::VARIABLE_TYPE_ARRAY)]
    public ?array $toolResult = null;
}
