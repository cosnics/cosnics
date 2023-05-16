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
class LibrariesConfiguration implements ConfigurationInterface
{

    /**
     * Builds and returns the node for doctrine
     *
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function addDoctrineNode()
    {
        $builder = new TreeBuilder('doctrine');
        $node = $builder->getRootNode();

        $mappingsConfiguration = new DoctrineORMMappingsConfiguration();

        $node->children()->arrayNode('orm')->children()->arrayNode('resolve_target_entities')->useAttributeAsKey(
                'baseEntity'
            )->requiresAtLeastOneElement()->prototype('scalar')->cannotBeEmpty()->end()->end()->end()->append(
                $mappingsConfiguration->buildRootNode()
            )->end()->end();

        return $node;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function addPHPStanNode()
    {
        $builder = new TreeBuilder('phpstan');
        $node = $builder->getRootNode();

        $node->children()->arrayNode('packages')->requiresAtLeastOneElement()->useAttributeAsKey('package')
            ->arrayPrototype()->children()->scalarNode('level')->end()->arrayNode('paths')->requiresAtLeastOneElement()
            ->prototype('scalar')->end()->end()->end()->end();

        return $node;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('chamilo_libraries');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->append($this->addDoctrineNode());
        $rootNode->append($this->addPHPStanNode());

        return $treeBuilder;
    }
}