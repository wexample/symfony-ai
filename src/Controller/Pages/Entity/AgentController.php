<?php

namespace Wexample\SymfonyAi\Controller\Pages\Entity;

use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Wexample\SymfonyAi\Entity\Agent;
use Wexample\SymfonyAi\Entity\Traits\Manipulator\AgentEntityManipulatorTrait;
use Wexample\SymfonyAi\Repository\AgentRepository;
use Wexample\SymfonyAi\Traits\SymfonyAiBundleClassTrait;
use Wexample\SymfonyLoader\Controller\AbstractEntityPagesController;

/**
 * The agents declared around, whichever app declares them.
 *
 * Read only: an agent is a file, and this shows what the last reading of that
 * file said.
 */
#[Route(path: 'agent/', name: 'entity_agent_')]
class AgentController extends AbstractEntityPagesController
{
    use AgentEntityManipulatorTrait;
    use SymfonyAiBundleClassTrait;

    final public const string ROUTE_INDEX = self::DEFAULT_ROUTE_NAME_INDEX;
    final public const string ROUTE_SHOW = self::DEFAULT_ROUTE_NAME_SHOW;

    #[Route(name: self::ROUTE_INDEX, path: '')]
    public function index(
        AgentRepository $agentRepository
    ): Response {
        return $this->renderPage(self::ROUTE_INDEX, [
            'agents' => $agentRepository->findBy([], ['name' => AgentRepository::SORT_ASC]),
        ]);
    }

    #[Route(name: self::ROUTE_SHOW, path: '{id}/', options: self::ROUTE_OPTIONS_ONLY_EXPOSE)]
    public function show(
        #[MapEntity] Agent $agent
    ): Response {
        return $this->renderPage(self::ROUTE_SHOW, [
            'agent' => $agent,
        ]);
    }
}
