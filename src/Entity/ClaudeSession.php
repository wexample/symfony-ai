<?php

namespace Wexample\SymfonyAi\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Wexample\Pseudocode\Attribute\PseudocodeExport;
use Wexample\SymfonyAi\Repository\ClaudeSessionRepository;

/**
 * A conversation held by Claude, projected from its record file.
 *
 * The transcript belongs to the engine and stays out — `sdkId` is the thread
 * back to it. Like an agent, the record is named by its own identity —
 * `<uuid>.yml` — so the row takes that uuid and a file that is gone leaves no
 * row behind.
 */
#[PseudocodeExport(inherited: true)]
#[ORM\Entity(repositoryClass: ClaudeSessionRepository::class)]
#[ORM\Table(name: 'claude_session')]
class ClaudeSession extends Session
{
    /** The record the session is read from, which is what says the app it belongs to. */
    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    protected string $path;

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
