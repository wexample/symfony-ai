<?php

namespace Wexample\SymfonyAi\Service;

use Wexample\SymfonyLoader\Interface\LayoutRenderDataProviderInterface;

/**
 * Tells the page what its agent tools may offer, with the layout's render
 * data (`agentTools`): the loader's AgentToolsService reads it, and lists
 * nothing the application turned off.
 */
final readonly class LayoutAgentToolsProvider implements LayoutRenderDataProviderInterface
{
    public function __construct(
        private AgentBackToolsService $agentBackTools,
    ) {
    }

    public function provideLayoutRenderData(): array
    {
        return ['agentTools' => $this->agentBackTools->frontSettings()];
    }
}
