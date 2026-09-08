<?php

namespace Wexample\SymfonyAi\Api\Dto;

use Wexample\SymfonyAi\Entity\Agent;
use Wexample\SymfonyApi\Api\Dto\AbstractEntityDto;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;

class AgentDto extends AbstractEntityDto
{
    public string $name;

    public ?string $description;

    public string $path;

    /** Null while the file names a model the catalogue does not hold. */
    public ?ModelDto $model;

    /**
     * @param Agent $entity
     */
    public static function fromEntity(AbstractEntity $entity): self
    {
        $dto = parent::fromEntity($entity);

        $dto->name = $entity->getName();
        $dto->description = $entity->getDescription();
        $dto->path = $entity->getPath();
        $dto->model = $entity->getModel() ? ModelDto::fromEntity($entity->getModel()) : null;

        return $dto;
    }
}
