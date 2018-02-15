<?php

namespace Chamilo\Core\Repository\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to collect the integration ContentObjectPublicationManager objects
 *
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectPublicationManagerCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('chamilo.core.repository.publication.service.content_object_publication_manager'))
        {
            $taggedServices = $container->findTaggedServiceIds(
                'chamilo.core.repository.publication.content_object_publication_manager'
            );

            $definition =  $container->getDefinition(
                'chamilo.core.repository.publication.service.content_object_publication_manager'
            );

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addContentObjectPublicationManager', array(new Reference($taggedServiceId))
                );
            }
        }
    }
}
