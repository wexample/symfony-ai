<?php

namespace Wexample\SymfonyAi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Wexample\SymfonyAi\Repository\AgentRepository;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\HasDescriptionTrait;
use Wexample\SymfonyHelpers\Entity\Traits\HasNameTrait;

/**
 * An agent a repository declares, projected into a row.
 *
 * The file owns what it says: this row is rebuilt from it and never written
 * back to it. The identity comes from the path, so the same file read twice
 * writes the same row, and a file that is gone leaves no row behind.
 */
#[ORM\Entity(repositoryClass: AgentRepository::class)]
#[ORM\Table(name: 'ai_agent')]
class Agent extends AbstractEntity
{
    public const ID_NAMESPACE = '5d2b8c40-31a7-5e69-8f14-6c0b93ae27d5';

    use HasDescriptionTrait;
    use HasNameTrait;

    /** The file the agent is declared in, which is what tells it apart. */
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
        $this->name = pathinfo($path, PATHINFO_FILENAME);

        $this->setId(self::idFor($path));
    }

    public static function idFor(string $path): Uuid
    {
        return Uuid::v5(Uuid::fromString(self::ID_NAMESPACE), $path);
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
