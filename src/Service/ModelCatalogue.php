<?php

namespace Wexample\SymfonyAi\Service;

use Doctrine\ORM\EntityManagerInterface;
use Wexample\SymfonyAi\Entity\Model;
use Wexample\SymfonyAi\Repository\ModelRepository;

/**
 * The models that exist, as the package lists them.
 *
 * A model is a fact about the outside world rather than about whoever calls it:
 * the same `claude-opus-4-7` is reached from a command line, from a browser or
 * through a third party, so the list belongs here and not to any of them. It is
 * shipped as JSON so that something other than PHP can read it.
 *
 * Only the models an agent can reason with are listed. Drawing, speaking and
 * transcribing are other jobs, and the row says nothing that would tell them
 * apart yet.
 */
final readonly class ModelCatalogue
{
    public const FILE = __DIR__.'/../Resources/data/models.json';

    public function __construct(
        private ModelRepository $repository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Puts a row behind every listed model, and leaves the others alone.
     *
     * A model dropped from the list keeps its row: an agent may still name it,
     * and a row that stays says what that agent was pointing at.
     *
     * @return array{0: int, 1: int} how many models are listed, and how many rows were added
     */
    public function seed(): array
    {
        $listed = 0;
        $added = 0;

        foreach ($this->read() as $provider => $names) {
            foreach ($names as $name) {
                ++$listed;

                $model = new Model($provider, $name);

                // The identity comes from the reference, so a catalogue seeded
                // twice holds one row per model rather than two.
                if ($this->repository->find($model->getId())) {
                    continue;
                }

                $this->entityManager->persist($model);
                ++$added;
            }
        }

        $this->entityManager->flush();

        return [$listed, $added];
    }

    /**
     * @return array<string, string[]> the models of each provider
     */
    private function read(): array
    {
        return json_decode(
            file_get_contents(self::FILE),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
