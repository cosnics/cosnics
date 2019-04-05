<?php

namespace Chamilo\Application\Lti\DependencyInjection\CompilerPass;

use Chamilo\Application\Lti\Service\Integration\IntegrationInterface;
use Chamilo\Application\Lti\Service\Outcome\IntegrationLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to collect the workspace extensions
 *
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class IntegrationCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(IntegrationLocator::class))
        {
            $taggedServices = $container->findTaggedServiceIds(IntegrationInterface::class);
            $definition =  $container->getDefinition(IntegrationLocator::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addIntegration', array(new Reference($taggedServiceId)));
            }
        }
    }
}
