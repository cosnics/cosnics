<?php

namespace Chamilo\Core\Repository\DependencyInjection\CompilerPass;

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
class WorkspaceExtensionCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('chamilo.core.repository.service.workspace_extension_manager'))
        {
            $taggedServices = $container->findTaggedServiceIds(
                'chamilo.core.repository.workspace.extension'
            );

            $definition =  $container->getDefinition(
                'chamilo.core.repository.service.workspace_extension_manager'
            );

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addExtension', array(new Reference($taggedServiceId))
                );
            }
        }
    }
}
