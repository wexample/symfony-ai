<?php

namespace Wexample\SymfonyAi\Service;

use Wexample\SymfonyAi\Entity\Agent;
use Wexample\SymfonyAi\Entity\Model;
use Wexample\SymfonyAi\Repository\ModelRepository;

/**
 * Translates an agent between a plain array and its row, both ways.
 *
 * Where that array is read from and written to is not known here: a caller
 * holding a file, a payload or a fixture passes the same thing.
 */
final readonly class AgentHydrator
{
    public const KEY_DESCRIPTION = 'description';
    public const KEY_MODEL = 'model';

    public function __construct(
        private ModelRepository $modelRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $values
     */
    public function hydrate(
        Agent $agent,
        array $values
    ): Agent {
        return $agent
            ->setDescription($values[self::KEY_DESCRIPTION] ?? null)
            ->setModel($this->model($values[self::KEY_MODEL] ?? null));
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(Agent $agent): array
    {
        return [
            self::KEY_DESCRIPTION => $agent->getDescription(),
            self::KEY_MODEL => $agent->getModel()?->getReference(),
        ];
    }

    /**
     * The model that reference names, or null when the catalogue holds no such
     * model: an agent naming one that is gone is still an agent.
     */
    private function model(?string $reference): ?Model
    {
        return $reference
            ? $this->modelRepository->find(Model::idFor($reference))
            : null;
    }
}
