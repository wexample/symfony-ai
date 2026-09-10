<?php

namespace Wexample\SymfonyAi\Api\Dto;

use Wexample\SymfonyAi\Entity\Message;
use Wexample\SymfonyApi\Api\Dto\AbstractEntityDto;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;

class MessageDto extends AbstractEntityDto
{
    /** Named after the property of the entity, which is what the browser checks against. */
    public string $session;

    /** `user`, `assistant` or `system`. */
    public ?string $type;

    public ?string $body;

    public ?string $dateCreated;

    public ?int $tokens;

    public ?string $providerMessageIdentifier;

    /**
     * @param Message $entity
     */
    public static function fromEntity(AbstractEntity $entity): self
    {
        $dto = parent::fromEntity($entity);

        $dto->session = (string) $entity->getSession()->getId();
        $dto->type = $entity->getType();
        $dto->body = $entity->getBody();
        $dto->dateCreated = $entity->getDateCreated()?->format(DATE_ATOM);
        $dto->tokens = $entity->getTokens();
        $dto->providerMessageIdentifier = $entity->getProviderMessageIdentifier();

        return $dto;
    }
}
