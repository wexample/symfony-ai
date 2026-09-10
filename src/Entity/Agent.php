<?php

namespace Wexample\SymfonyAi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Wexample\Pseudocode\Attribute\PseudocodeExport;
use Wexample\SymfonyAi\Repository\AgentRepository;
use Wexample\SymfonyApi\Attribute\ApiEntity;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\HasDescriptionTrait;
use Wexample\SymfonyHelpers\Entity\Traits\HasNameTrait;

/**
 * An agent an app declares, projected into a record.
 *
 * The file owns what it says: this record is rebuilt from it and never written
 * back to it. The declaration is named by its own identity — `<uuid>.yml` — so
 * the record takes that uuid, an agent renamed or moved stays the same agent,
 * and a declaration that is gone leaves no record behind.
 */
#[ApiEntity]
#[PseudocodeExport(inherited: true)]
#[ORM\Entity(repositoryClass: AgentRepository::class)]
#[ORM\Table(name: 'agent')]
class Agent extends AbstractEntity
{
    use HasDescriptionTrait;
    use HasNameTrait;

    /** The file the agent is declared in, which is what says the app it belongs to. */
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    protected string $path;

    /**
     * Null while the file names a model the catalogue does not hold, which is
     * what a typo or an unconfigured provider looks like.
     */
    #[ORM\ManyToOne(targetEntity: Model::class)]
    protected ?Model $model = null;

    public function __construct(string $path)
    {
        parent::__construct();

        $this->path = $path;

        $this->setId(self::idFor($path));
    }

    /** The identity the declaration is named by, read straight off the file name. */
    public static function idFor(string $path): Uuid
    {
        return Uuid::fromString(pathinfo($path, PATHINFO_FILENAME));
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): self
    {
        $this->model = $model;

        return $this;
    }
}
