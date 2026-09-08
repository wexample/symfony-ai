<?php

namespace Wexample\SymfonyAi\Api\Dto;

use Wexample\SymfonyAi\Entity\Model;
use Wexample\SymfonyApi\Api\Dto\AbstractEntityDto;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;

class ModelDto extends AbstractEntityDto
{
    public string $name;

    public string $provider;

    /** What a file writes to point at it, `provider:name`. */
    public string $reference;

    /**
     * @param Model $entity
     */
    public static function fromEntity(AbstractEntity $entity): self
    {
        $dto = parent::fromEntity($entity);

        $dto->name = $entity->getName();
        $dto->provider = $entity->getProvider();
        $dto->reference = $entity->getReference();

        return $dto;
    }
}
