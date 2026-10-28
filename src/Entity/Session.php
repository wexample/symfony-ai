<?php

namespace Wexample\SymfonyAi\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Wexample\Pseudocode\Attribute\PseudocodeExport;
use Wexample\SymfonyAi\Repository\SessionRepository;
use Wexample\SymfonyApi\Attribute\ApiEntity;
use Wexample\SymfonyForms\Attribute\EntityForm;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyLive\Attribute\LiveEntity;
use Wexample\SymfonyLive\Enum\LiveTopicAction;

/**
 * One conversation held in the app, whatever engine holds it.
 *
 * Everything about the conversation except the messages: who ran it, what it
 * was called, where it got to. What ties it to its engine — a transcript on
 * disk, a session on a remote server — is the engine's own and lives in the
 * subclass of that engine, each in a table of its own joined to this one.
 *
 * The root of the hierarchy is what the api, the live topics and the browser
 * know: every kind is a `session` to them, told apart by `engine`, which the
 * subclasses name themselves after — `ClaudeSession` is `claude`.
 */
#[ApiEntity]
#[EntityForm]
#[LiveEntity(actions: [LiveTopicAction::EVENT])]
#[PseudocodeExport(inherited: true)]
#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\Table(name: 'session')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'engine', type: Types::STRING, length: 32)]
abstract class Session extends AbstractEntity
{
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

}
