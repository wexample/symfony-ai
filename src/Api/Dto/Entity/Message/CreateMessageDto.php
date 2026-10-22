<?php

namespace Wexample\SymfonyAi\Api\Dto\Entity\Message;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Wexample\SymfonyApi\Api\Attribute\RequiredDtoProperty;
use Wexample\SymfonyApi\Api\Dto\AbstractDto;
use Wexample\SymfonyHelpers\Helper\VariableHelper;

/**
 * What has to be said to speak in a conversation: which one, and what.
 *
 * The type is deliberately not asked for. A turn written through this endpoint
 * was spoken by the operator, and letting the caller name its author would let
 * it write the answers too.
 */
class CreateMessageDto extends AbstractDto
{
    #[Type(VariableHelper::VARIABLE_TYPE_STRING)]
    #[RequiredDtoProperty]
    public string $session;

    #[Type(VariableHelper::VARIABLE_TYPE_STRING)]
    #[NotBlank]
    #[RequiredDtoProperty]
    public string $body;
}
