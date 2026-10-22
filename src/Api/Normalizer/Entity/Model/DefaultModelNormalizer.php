<?php

namespace Wexample\SymfonyAi\Api\Normalizer\Entity\Model;

use ArrayObject;
use Wexample\SymfonyAi\Api\Dto\ModelDto;
use Wexample\SymfonyAi\Entity\Model;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\ModelEntityManipulatorTrait;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Interface\NormalizableDataInterface;
use Wexample\SymfonyHelpers\Normalizer\AbstractEntityNormalizer;

class DefaultModelNormalizer extends AbstractEntityNormalizer
{
    use ModelEntityManipulatorTrait;

    public function normalizeEntity(
        Model|AbstractEntity $entity,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|NormalizableDataInterface|null {
        return ModelDto::fromEntity($entity);
    }
}
