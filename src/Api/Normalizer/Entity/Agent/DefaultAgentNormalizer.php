<?php

namespace Wexample\SymfonyAi\Api\Normalizer\Entity\Agent;

use Wexample\SymfonyAi\Api\Dto\AgentDto;
use Wexample\SymfonyAi\Entity\Agent;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\AgentEntityManipulatorTrait;
use ArrayObject;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Interface\NormalizableDataInterface;
use Wexample\SymfonyHelpers\Normalizer\AbstractEntityNormalizer;

class DefaultAgentNormalizer extends AbstractEntityNormalizer
{
    use AgentEntityManipulatorTrait;

    public function normalizeEntity(
        Agent|AbstractEntity $entity,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|NormalizableDataInterface|null {
        return AgentDto::fromEntity($entity);
    }
}
