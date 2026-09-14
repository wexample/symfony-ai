<?php

namespace Wexample\SymfonyAi\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Wexample\Pseudocode\Attribute\PseudocodeExport;
use Wexample\SymfonyAi\Repository\ModelRepository;
use Wexample\SymfonyApi\Attribute\ApiEntity;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\HasDescriptionTrait;
use Wexample\SymfonyHelpers\Entity\Traits\HasNameTrait;

/**
 * One model, under the one name a file points at it by: `claude-opus-5`.
 *
 * That name is opaque. Nothing splits it to find the maker or the identifier
 * the vendor's own API answers to, because both are fields of their own here —
 * a maker names its range as it pleases, and pins dated snapshots it later
 * bumps, so a name taken apart is a name that breaks at the next release.
 *
 * No app declares it and nothing here lists it either: the list belongs to
 * whoever runs the turns and refuses an unknown name, so the record is
 * projected from there, uuid included. Offering a model that executor would
 * reject is the one thing this record exists to prevent.
 *
 * Only the models an agent can reason with are listed. Drawing, speaking and
 * transcribing are other jobs, and the record says nothing that would tell them
 * apart yet.
 */
#[ApiEntity]
#[PseudocodeExport(inherited: true)]
#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ORM\Table(name: 'model')]
#[ORM\UniqueConstraint(columns: ['name'])]
class Model extends AbstractEntity
{
    use HasDescriptionTrait;
    use HasNameTrait;

    /** Who builds it: `anthropic`, `openai`. Shown, never dispatched on. */
    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $maker;

    /**
     * The identifier the vendor's API answers to, which is the only form that
     * ever goes on the wire — dated where the vendor pins snapshots.
     */
    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $apiId;

    /**
     * Its rank in the list it comes from, the most capable first. That order is
     * a judgement nobody here could rebuild by sorting.
     */
    #[ORM\Column(type: Types::INTEGER)]
    protected int $position = 0;

    public function __construct(Uuid $id)
    {
        parent::__construct();

        $this->setId($id);
    }

    public function getMaker(): string
    {
        return $this->maker;
    }

    public function setMaker(string $maker): self
    {
        $this->maker = $maker;

        return $this;
    }

    public function getApiId(): string
    {
        return $this->apiId;
    }

    public function setApiId(string $apiId): self
    {
        $this->apiId = $apiId;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
