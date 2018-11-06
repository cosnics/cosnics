<?php

namespace Chamilo\Core\Repository\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to collect the integration PublicationAggregator objects
 *
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationAggregatorCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(
            'chamilo.core.repository.publication.service.publication_aggregator'
        ))
        {
            $taggedServices = $container->findTaggedServiceIds(
                'chamilo.core.repository.publication.publication_aggregator'
            );

            $definition = $container->getDefinition(
                'chamilo.core.repository.publication.service.publication_aggregator'
            );

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addPublicationAggregator', array(new Reference($taggedServiceId))
                );
            }
        }
    }
}
