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
class DoctrineORMMappingsConfiguration implements ConfigurationInterface
{
    private TreeBuilder $treeBuilder;

    public function __construct()
    {
        $this->treeBuilder = new TreeBuilder('mappings');
    }

    public function buildRootNode(): NodeDefinition
    {
        $rootNode = $this->treeBuilder->getRootNode();

        $rootNode->children()->arrayNode('default')->requiresAtLeastOneElement()->prototype('scalar')->cannotBeEmpty()
            ->end()->end()->arrayNode(
                'custom'
            )->prototype('array')->children()->enumNode('type')->values(
                ['annotation', 'xml', 'yaml', 'php', 'staticphp']
            )->isRequired()->cannotBeEmpty()->end()->scalarNode(
                'namespace'
            )->isRequired()->cannotBeEmpty()->end()->arrayNode('paths')->requiresAtLeastOneElement()->prototype(
                'scalar'
            )->cannotBeEmpty()->end()->isRequired()->end()->end()->end()->end();

        return $rootNode;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $this->buildRootNode();

        return $this->treeBuilder;
    }
}