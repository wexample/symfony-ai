<?php

namespace Wexample\SymfonyAi\Api\Normalizer\Entity\Message;

use Wexample\SymfonyAi\Api\Dto\MessageDto;
use Wexample\SymfonyAi\Entity\Message;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\MessageEntityManipulatorTrait;
use ArrayObject;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Interface\NormalizableDataInterface;
use Wexample\SymfonyHelpers\Normalizer\AbstractEntityNormalizer;

class DefaultMessageNormalizer extends AbstractEntityNormalizer
{
    use MessageEntityManipulatorTrait;

    public function normalizeEntity(
        Message|AbstractEntity $entity,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|NormalizableDataInterface|null {
        return MessageDto::fromEntity($entity);
    }
}
