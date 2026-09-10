<?php

namespace Wexample\SymfonyAi\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Wexample\Pseudocode\Attribute\PseudocodeExport;
use Wexample\SymfonyAi\Repository\MessageRepository;
use Wexample\SymfonyApi\Attribute\ApiEntity;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\HasBodyTrait;
use Wexample\SymfonyHelpers\Entity\Traits\HasDateCreatedTrait;
use Wexample\SymfonyHelpers\Entity\Traits\HasTypeTrait;

/**
 * One turn of a conversation: who spoke, what was said, when.
 *
 * Only what every engine has. A provider's own transcript says a great deal
 * more — Claude's holds titles, queue operations and a parent link per line —
 * and none of it survives a change of provider, so none of it is stored here.
 * `providerMessageIdentifier` is the one thread back to the original.
 */
#[ApiEntity]
#[PseudocodeExport(inherited: true)]
#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'message')]
class Message extends AbstractEntity
{
    use HasBodyTrait;
    use HasDateCreatedTrait;
    use HasTypeTrait;

    public const TYPE_ASSISTANT = 'assistant';
    public const TYPE_SYSTEM = 'system';
    public const TYPE_USER = 'user';

    /**
     * Deleted with the conversation: a session is dropped as soon as its record
     * leaves the disk, and what was said in it has nowhere to be said any more.
     */
    #[ORM\ManyToOne(targetEntity: Session::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected Session $session;

    /**
     * What the provider calls this message — Claude's per-line `uuid`. Null for
     * a provider that names nothing, unique so the same transcript read twice
     * yields one row.
     */
    #[ORM\Column(type: Types::STRING, length: 255, unique: true, nullable: true)]
    protected ?string $providerMessageIdentifier = null;

    /** What the turn cost, when the provider says. */
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    protected ?int $tokens = null;

    public function __construct(Session $session)
    {
        parent::__construct();

        $this->session = $session;
    }

    /** @return string[] */
    public static function getAllowedTypes(): ?array
    {
        return [
            self::TYPE_ASSISTANT,
            self::TYPE_SYSTEM,
            self::TYPE_USER,
        ];
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function getProviderMessageIdentifier(): ?string
    {
        return $this->providerMessageIdentifier;
    }

    public function setProviderMessageIdentifier(?string $providerMessageIdentifier): self
    {
        $this->providerMessageIdentifier = $providerMessageIdentifier;

        return $this;
    }

    public function getTokens(): ?int
    {
        return $this->tokens;
    }

    public function setTokens(?int $tokens): self
    {
        $this->tokens = $tokens;

        return $this;
    }
}
