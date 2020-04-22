<?php

namespace Chamilo\Libraries\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration class to validate the configuration for this package
 *
 * @package Chamilo\Libraries\DependencyInjection\Configuration
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineORMMappingsConfiguration implements ConfigurationInterface
{

    /**
     * The Tree Builder
     *
     * @var \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    private $treeBuilder;

    public function __construct()
    {
        $this->treeBuilder = new TreeBuilder();
    }

    /**
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public function buildRootNode()
    {
        $rootNode = $this->treeBuilder->root('mappings');

        $rootNode->children()->arrayNode('default')->requiresAtLeastOneElement()->prototype('scalar')->cannotBeEmpty()
            ->end()->end()->arrayNode(
                'custom'
            )->prototype('array')->children()->enumNode('type')->values(
                array('annotation', 'xml', 'yaml', 'php', 'staticphp')
            )->isRequired()->cannotBeEmpty()->end()->scalarNode(
                'namespace'
            )->isRequired()->cannotBeEmpty()->end()->arrayNode('paths')->requiresAtLeastOneElement()->prototype(
                'scalar'
            )->cannotBeEmpty()->end()->isRequired()->end()->end()->end()->end();

        return $rootNode;
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
}