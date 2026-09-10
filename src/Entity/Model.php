<?php

namespace Wexample\SymfonyAi\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Wexample\Pseudocode\Attribute\PseudocodeExport;
use Wexample\SymfonyAi\Repository\ModelRepository;
use Wexample\SymfonyApi\Attribute\ApiEntity;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\HasNameTrait;

/**
 * One model, under the one name a file points at it by: `claude-opus-5`.
 *
 * That name is opaque. Nothing splits it to find the maker or the identifier
 * the vendor's own API answers to, because both are fields of their own here —
 * a maker names its range as it pleases, and pins dated snapshots it later
 * bumps, so a name taken apart is a name that breaks at the next release.
 *
 * No app declares it: unlike an agent, it comes from the list the package ships
 * and is seeded rather than projected. Its identity derives from the name, so a
 * catalogue seeded twice holds one record per model, not two.
 */
#[ApiEntity]
#[PseudocodeExport(inherited: true)]
#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ORM\Table(name: 'model')]
#[ORM\UniqueConstraint(columns: ['name'])]
class Model extends AbstractEntity
{
    use HasNameTrait;
    public const ID_NAMESPACE = 'c1f0a4d2-8e5b-5f37-b2a4-9d6e0c7b1a83';

    /** Who builds it: `anthropic`, `openai`. Shown, never dispatched on. */
    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $maker;

    /**
     * The identifier the vendor's API answers to, which is the only form that
     * ever goes on the wire — dated where the vendor pins snapshots.
     */
    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $apiId;

    public function __construct(
        string $name,
        string $maker,
        string $apiId
    ) {
        parent::__construct();

        $this->name = $name;
        $this->maker = $maker;
        $this->apiId = $apiId;

        $this->setId(self::idFor($name));
    }

    public static function idFor(string $name): Uuid
    {
        return Uuid::v5(Uuid::fromString(self::ID_NAMESPACE), $name);
    }

    public function getMaker(): string
    {
        return $this->maker;
    }

    public function getApiId(): string
    {
        return $this->apiId;
    }
}
