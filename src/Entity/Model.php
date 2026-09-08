<?php

namespace Wexample\SymfonyAi\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Wexample\SymfonyAi\Repository\ModelRepository;
use Wexample\SymfonyHelpers\Entity\AbstractEntity;
use Wexample\SymfonyHelpers\Entity\Traits\HasNameTrait;

/**
 * One model of one maker, as a file names it: `anthropic:claude-opus-4-7`.
 *
 * No app declares it: unlike an agent, it comes from the list the package
 * ships and is seeded rather than projected. Its identity still derives from
 * the reference, so a catalogue seeded twice holds one row per model, not two.
 */
#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ORM\Table(name: 'ai_model')]
#[ORM\UniqueConstraint(columns: ['provider', 'name'])]
class Model extends AbstractEntity
{
    public const ID_NAMESPACE = 'c1f0a4d2-8e5b-5f37-b2a4-9d6e0c7b1a83';

    public const REFERENCE_SEPARATOR = ':';

    use HasNameTrait;

    /**
     * Who makes it: `anthropic`, `openai`. Not who serves it — the same model
     * is reached through several of them, and that is the runner's question.
     */
    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $provider;

    public function __construct(
        string $provider,
        string $name
    ) {
        parent::__construct();

        $this->provider = $provider;
        $this->name = $name;

        $this->setId(self::idFor($this->getReference()));
    }

    /**
     * @param string $reference as a file writes it, `provider:name`
     */
    public static function fromReference(string $reference): self
    {
        [$provider, $name] = explode(self::REFERENCE_SEPARATOR, $reference, 2);

        return new self($provider, $name);
    }

    public static function idFor(string $reference): Uuid
    {
        return Uuid::v5(Uuid::fromString(self::ID_NAMESPACE), $reference);
    }

    /**
     * What a file names the model by, and what an agent points at.
     */
    public function getReference(): string
    {
        return $this->provider.self::REFERENCE_SEPARATOR.$this->name;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }
}
