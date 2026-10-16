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
 * An agent, projected into a record: one an app declares, or one wex ships.
 *
 * The source owns what it says: this record is rebuilt from it and never
 * written back to it. An app's agent is declared in a file named by its own
 * identity — `<uuid>.json` — so the record takes that uuid, an agent renamed
 * or moved stays the same agent, and a declaration that is gone leaves no
 * record behind. An agent a package ships has no file: wex lists it, with an
 * identity that follows from its qualified name, and it is read only.
 */
#[ApiEntity]
#[PseudocodeExport(inherited: true)]
#[ORM\Entity(repositoryClass: AgentRepository::class)]
#[ORM\Table(name: 'agent')]
class Agent extends AbstractEntity
{
    use HasDescriptionTrait;
    use HasNameTrait;

    /**
     * The file the agent is declared in, which is what says the app it belongs
     * to; null for an agent a package ships, which belongs to none.
     */
    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    protected ?string $path = null;

    /**
     * Where wex says the agent comes from — `addon:ai/editor` — for an agent a
     * package ships; null for an app's own, which its path already places.
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $qualifiedName = null;

    /**
     * Null while the file names a model the catalogue does not hold, which is
     * what a typo or an unconfigured provider looks like.
     */
    #[ORM\ManyToOne(targetEntity: Model::class)]
    protected ?Model $model = null;

    /**
     * @param string|null $path the declaration of an app's agent
     * @param Uuid|null   $id   the identity wex gives an agent it ships, which has no path
     */
    public function __construct(
        ?string $path = null,
        ?Uuid $id = null,
    ) {
        parent::__construct();

        $this->path = $path;

        $this->setId(null !== $path ? self::idFor($path) : $id);
    }

    /** The identity the declaration is named by, read straight off the file name. */
    public static function idFor(string $path): Uuid
    {
        return Uuid::fromString(pathinfo($path, PATHINFO_FILENAME));
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    /** Whether a package ships it: then nothing in an app declares it, and nothing here edits it. */
    public function isBuiltIn(): bool
    {
        return null === $this->path;
    }

    public function getQualifiedName(): ?string
    {
        return $this->qualifiedName;
    }

    public function setQualifiedName(?string $qualifiedName): self
    {
        $this->qualifiedName = $qualifiedName;

        return $this;
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
