<?php

namespace Wexample\SymfonyAi\Api\Normalizer\Entity\ClaudeSession;

use ArrayObject;
use Wexample\SymfonyAi\Api\Dto\ClaudeSessionDto;
use Wexample\SymfonyAi\Api\Normalizer\Entity\Session\DefaultSessionNormalizer;
use Wexample\SymfonyAi\Entity\ClaudeSession;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\ClaudeSessionEntityManipulatorTrait;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Interface\NormalizableDataInterface;

/**
 * A Claude session under the session envelope: the kind's own words added to
 * every session's, and the engine named in the row.
 */
class DefaultClaudeSessionNormalizer extends DefaultSessionNormalizer
{
    use ClaudeSessionEntityManipulatorTrait;

    public function normalizeEntity(
        ClaudeSession|AbstractEntity $entity,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|NormalizableDataInterface|null {
        return ClaudeSessionDto::fromEntity($entity);
    }
}
