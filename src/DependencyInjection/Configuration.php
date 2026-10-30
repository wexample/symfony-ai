<?php

namespace Wexample\SymfonyAi\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * What the application lets the agents use (AgentBackToolsService): the
 * page's tools (`front`), the server's (`back`), and tools turned off by
 * name whichever side runs them (`disabled`).
 *
 *     wexample_symfony_ai:
 *         agent_tools:
 *             front: true
 *             back: true
 *             disabled: [navigate]
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('wexample_symfony_ai');

        $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('agent_tools')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('front')->defaultTrue()->end()
            ->booleanNode('back')->defaultTrue()->end()
            ->arrayNode('disabled')
            ->scalarPrototype()->cannotBeEmpty()->end()
            ->defaultValue([])
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
