<?php

namespace Wexample\SymfonyAi\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wexample\SymfonyAi\Interface\AgentBackToolInterface;
use Wexample\SymfonyHelpers\DependencyInjection\AbstractWexampleSymfonyExtension;

class WexampleSymfonyAiExtension extends AbstractWexampleSymfonyExtension
{
    final public const string PARAMETER_AGENT_TOOLS = 'wexample_symfony_ai.agent_tools';

    public function load(
        array $configs,
        ContainerBuilder $container
    ): void {
        $this->loadConfig(
            __DIR__,
            $container
        );

        // A service implementing the interface is a tool of the server,
        // whichever bundle ships it: the tag the registry collects follows.
        $container
            ->registerForAutoconfiguration(AgentBackToolInterface::class)
            ->addTag(AgentBackToolInterface::TAG);

        $config = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter(self::PARAMETER_AGENT_TOOLS, $config['agent_tools']);
    }
}
