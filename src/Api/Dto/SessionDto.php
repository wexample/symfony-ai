<?php

namespace Wexample\SymfonyAi\Api\Dto;

use Wexample\SymfonyAi\Entity\Session;
use Wexample\SymfonyApi\Api\Dto\AbstractEntityDto;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;

/**
 * What every kind of session says of itself; a kind says the rest in a dto
 * built on this one.
 */
class SessionDto extends AbstractEntityDto
{
    /** What the operator called the conversation, null while it has no name. */
    public ?string $name;

    /** Qualified name, which is what a package agent has instead of a record. */
    public string $agentName;

    public ?AgentDto $agent;

    public ?ModelDto $model;

    public int $turnCount;

    public ?string $dateCreated;

    public ?string $dateLastMessage;

    /**
     * @param Session $entity
     */
    public static function fromEntity(AbstractEntity $entity): self
    {
        $dto = parent::fromEntity($entity);

        $dto->name = $entity->getName();
        $dto->agentName = $entity->getAgentName();
        $dto->agent = $entity->getAgent() ? AgentDto::fromEntity($entity->getAgent()) : null;
        $dto->model = $entity->getModel() ? ModelDto::fromEntity($entity->getModel()) : null;
        $dto->turnCount = $entity->getTurnCount();
        $dto->dateCreated = $entity->getDateCreated()?->format(DATE_ATOM);
        $dto->dateLastMessage = $entity->getDateLastMessage()?->format(DATE_ATOM);

        return $dto;
    }
}
