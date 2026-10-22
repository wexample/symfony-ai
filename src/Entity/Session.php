<?php

namespace Wexample\SymfonyAi\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Wexample\Pseudocode\Attribute\PseudocodeExport;
use Wexample\SymfonyAi\Repository\SessionRepository;
use Wexample\SymfonyApi\Attribute\ApiEntity;
use Wexample\SymfonyForms\Attribute\EntityForm;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyLive\Attribute\LiveEntity;
use Wexample\SymfonyLive\Enum\LiveTopicAction;

/**
 * One conversation held in the app, projected from its record file.
 *
 * Everything about the conversation except the messages: who ran it, what it
 * was called, where it got to. The transcript belongs to the engine and stays
 * out — `sdkId` is the thread back to it.
 *
 * Like an agent, the record is named by its own identity — `<uuid>.yml` — so
 * the row takes that uuid and a file that is gone leaves no row behind.
 */
#[ApiEntity]
#[EntityForm]
#[LiveEntity(actions: [LiveTopicAction::EVENT])]
#[PseudocodeExport(inherited: true)]
#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\Table(name: 'session')]
class Session extends AbstractEntity
{
    /** The record the session is read from, which is what says the app it belongs to. */
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    protected string $path;

    /**
     * Null for an agent a package ships: that one is code, it has no record,
     * and `agentName` is all there is to remember it by.
     */
    #[ORM\ManyToOne(targetEntity: Agent::class)]
    protected ?Agent $agent = null;

    /** Qualified name the operator recognises: `app:main`, `service:vite/main`. */
    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $agentName;

    /** What the operator called the conversation, null while it has no name. */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $name = null;

    /**
     * The model this conversation was pinned to, null while the agent's own
     * still decides.
     */
    #[ORM\ManyToOne(targetEntity: Model::class)]
    protected ?Model $model = null;

    #[ORM\Column(type: Types::INTEGER)]
    protected int $turnCount = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTimeInterface $dateCreated = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTimeInterface $dateLastMessage = null;

    /** The engine's own identifier, null until the first turn has been answered. */
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $sdkId = null;

    public function __construct(string $path)
    {
        parent::__construct();

        $this->path = $path;

        $this->setId(self::idFor($path));
    }

    /** The identity the record is named by, read straight off the file name. */
    public static function idFor(string $path): Uuid
    {
        return Uuid::fromString(pathinfo($path, PATHINFO_FILENAME));
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getAgentName(): string
    {
        return $this->agentName;
    }

    public function setAgentName(string $agentName): self
    {
        $this->agentName = $agentName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getTurnCount(): int
    {
        return $this->turnCount;
    }

    public function setTurnCount(int $turnCount): self
    {
        $this->turnCount = $turnCount;

        return $this;
    }

    public function getDateCreated(): ?DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateLastMessage(): ?DateTimeInterface
    {
        return $this->dateLastMessage;
    }

    public function setDateLastMessage(?DateTimeInterface $dateLastMessage): self
    {
        $this->dateLastMessage = $dateLastMessage;

        return $this;
    }

    public function getSdkId(): ?string
    {
        return $this->sdkId;
    }

    public function setSdkId(?string $sdkId): self
    {
        $this->sdkId = $sdkId;

        return $this;
    }
}
