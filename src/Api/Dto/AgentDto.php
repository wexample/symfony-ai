<?php

namespace Wexample\SymfonyAi\Api\Dto;

use Wexample\SymfonyAi\Entity\Agent;
use Wexample\SymfonyApi\Api\Dto\AbstractEntityDto;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;

class AgentDto extends AbstractEntityDto
{
    public string $name;

    public ?string $description;

    /** Null for an agent a package ships, which no file declares. */
    public ?string $path;

    /** Where wex says a shipped agent comes from — `addon:ai/editor`. */
    public ?string $qualifiedName;

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
        $dto->qualifiedName = $entity->getQualifiedName();
        $dto->model = $entity->getModel() ? ModelDto::fromEntity($entity->getModel()) : null;

        return $dto;
    }
}
