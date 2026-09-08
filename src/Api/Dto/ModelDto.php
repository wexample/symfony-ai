<?php

namespace Wexample\SymfonyAi\Api\Dto;

use Wexample\SymfonyAi\Entity\Model;
use Wexample\SymfonyApi\Api\Dto\AbstractEntityDto;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;

class ModelDto extends AbstractEntityDto
{
    /** What a file writes to point at it. */
    public string $name;

    public string $maker;

    public string $apiId;

    /**
     * @param Model $entity
     */
    public static function fromEntity(AbstractEntity $entity): self
    {
        $dto = parent::fromEntity($entity);

        $dto->name = $entity->getName();
        $dto->maker = $entity->getMaker();
        $dto->apiId = $entity->getApiId();

        return $dto;
    }
}
