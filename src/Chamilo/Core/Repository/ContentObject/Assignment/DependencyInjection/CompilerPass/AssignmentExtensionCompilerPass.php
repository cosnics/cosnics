<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\DependencyInjection\CompilerPass;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\Extensions\ExtensionInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\Extensions\ExtensionManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to collect the assignment extensions
 *
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentExtensionCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(ExtensionManager::class))
        {
            $taggedServices = $container->findTaggedServiceIds(ExtensionInterface::class);
            $definition =  $container->getDefinition(ExtensionManager::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addExtension', array(new Reference($taggedServiceId)));
            }
        }
    }
}
