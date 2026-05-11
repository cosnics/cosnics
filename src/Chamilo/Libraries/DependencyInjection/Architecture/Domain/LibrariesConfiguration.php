<?php
namespace Chamilo\Libraries\DependencyInjection\Architecture\Domain;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration class to validate the configuration for this package
 *
 * @package Chamilo\Libraries\DependencyInjection\Architecture\Domain
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LibrariesConfiguration implements ConfigurationInterface
{
    protected function addDoctrineNode(): NodeDefinition
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

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('chamilo_libraries');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->append($this->addDoctrineNode());

        return $treeBuilder;
    }
}