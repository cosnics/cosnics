<?php

namespace Chamilo\Libraries\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration class to validate the configuration for this package
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineORMMappingsConfiguration implements ConfigurationInterface
{
    /**
     * The Tree Builder
     *
     * @var TreeBuilder
     */
    private $treeBuilder;

    public function __construct()
    {
        $this->treeBuilder = new TreeBuilder();
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $this->buildRootNode();

        return $this->treeBuilder;
    }

    /**
     * Builds the root node for the tree builder
     */
    public function buildRootNode()
    {
        $rootNode = $this->treeBuilder->root('mappings');

        $rootNode->children()
            ->arrayNode('default')
                ->requiresAtLeastOneElement()
                ->prototype('scalar')
                    ->cannotBeEmpty()
                    ->end()
            ->end()
            ->arrayNode('custom')
                ->prototype('array')
                    ->cannotBeEmpty()
                    ->children()
                    ->enumNode('type')
                        ->values(array('annotation', 'xml', 'yaml', 'php', 'staticphp'))
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('namespace')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('paths')
                        ->requiresAtLeastOneElement()
                        ->prototype('scalar')
                            ->cannotBeEmpty()
                        ->end()
                        ->isRequired()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $rootNode;
    }
}