<?php

namespace Wexample\SymfonyAi\Service;

use DateTime;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;
use Wexample\SymfonyAi\Entity\Agent;
use Wexample\SymfonyAi\Entity\Model;
use Wexample\SymfonyAi\Entity\Session;
use Wexample\SymfonyAi\Repository\AgentRepository;
use Wexample\SymfonyAi\Repository\ModelRepository;

/**
 * Translates a session between a plain array and its record, both ways.
 *
 * Where that array is read from and written to is not known here, on the same
 * terms as `AgentHydrator`.
 */
final readonly class SessionHydrator
{
    public const KEY_AGENT_ID = 'agent_id';
    public const KEY_AGENT_NAME = 'agent_name';
    public const KEY_DATE_CREATED = 'date_created';
    public const KEY_DATE_LAST_MESSAGE = 'date_last_message';
    public const KEY_MODEL = 'model';
    public const KEY_NAME = 'name';
    public const KEY_SDK_ID = 'sdk_id';
    public const KEY_TURN_COUNT = 'turn_count';

    public function __construct(
        private AgentRepository $agentRepository,
        private ModelRepository $modelRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $values
     */
    public function hydrate(
        Session $session,
        array $values
    ): Session {
        return $session
            ->setName($values[self::KEY_NAME] ?? null)
            ->setAgentName($values[self::KEY_AGENT_NAME] ?? '')
            ->setAgent($this->agent($values[self::KEY_AGENT_ID] ?? null))
            ->setModel($this->model($values[self::KEY_MODEL] ?? null))
            ->setTurnCount((int) ($values[self::KEY_TURN_COUNT] ?? 0))
            ->setDateCreated($this->date($values[self::KEY_DATE_CREATED] ?? null))
            ->setDateLastMessage($this->date($values[self::KEY_DATE_LAST_MESSAGE] ?? null))
            ->setSdkId($values[self::KEY_SDK_ID] ?? null);
    }

    /**
     * @return array<string, mixed>
     */
    public function dump(Session $session): array
    {
        return [
            self::KEY_NAME => $session->getName(),
            self::KEY_AGENT_NAME => $session->getAgentName(),
            self::KEY_AGENT_ID => $session->getAgent() ? (string) $session->getAgent()->getId() : null,
            self::KEY_MODEL => $session->getModel()?->getName(),
            self::KEY_TURN_COUNT => $session->getTurnCount(),
            self::KEY_DATE_CREATED => $session->getDateCreated()?->format(DATE_ATOM),
            self::KEY_DATE_LAST_MESSAGE => $session->getDateLastMessage()?->format(DATE_ATOM),
            self::KEY_SDK_ID => $session->getSdkId(),
        ];
    }

    /**
     * The agent that ran the conversation, or null: an agent a package ships is
     * code, it has no record, and only its name survives.
     */
    private function agent(?string $id): ?Agent
    {
        return $id && Uuid::isValid($id)
            ? $this->agentRepository->find(Uuid::fromString($id))
            : null;
    }

    /**
     * The model the conversation was pinned to, null when the agent's own still
     * decides or when the catalogue holds no such model.
     */
    private function model(?string $name): ?Model
    {
        return $name
            ? $this->modelRepository->find(Model::idFor($name))
            : null;
    }

    private function date(?string $value): ?DateTimeInterface
    {
        return $value ? new DateTime($value) : null;
    }
}
