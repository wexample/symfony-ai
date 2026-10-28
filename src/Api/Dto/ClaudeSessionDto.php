<?php

namespace Wexample\SymfonyAi\Api\Dto;

use Wexample\SymfonyAi\Entity\ClaudeSession;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;

/**
 * What a Claude session says beyond what every session does: where its
 * record is, and the thread back to its transcript.
 */
class ClaudeSessionDto extends SessionDto
{
    public string $path;

    public ?string $sdkId;

    /**
     * @param ClaudeSession $entity
     */
    public static function fromEntity(AbstractEntity $entity): self
    {
        $dto = parent::fromEntity($entity);

        $dto->path = $entity->getPath();
        $dto->sdkId = $entity->getSdkId();

        return $dto;
    }
}
