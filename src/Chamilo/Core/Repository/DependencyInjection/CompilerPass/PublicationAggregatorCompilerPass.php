<?php
namespace Chamilo\Core\Repository\DependencyInjection\CompilerPass;

use Chamilo\Core\Repository\Publication\Service\PublicationAggregator;
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
        if ($container->hasDefinition(PublicationAggregator::class))
        {
            $taggedServices = $container->findTaggedServiceIds(
                'chamilo.core.repository.publication.publication_aggregator'
            );

            $definition = $container->getDefinition(PublicationAggregator::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addPublicationAggregator', array(new Reference($taggedServiceId))
                );
            }
        }
    }
}
