<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TaggedServicesCompilerPass
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
abstract class TaggedServicesCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     * @param string $serviceClassName
     * @param string $tagName
     * @param string $method
     */
    protected function addTaggedServicesToService(
        ContainerBuilder $container, string $serviceClassName, string $tagName, string $method
    )
    {
        if (!$container->hasDefinition($serviceClassName))
        {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds($tagName);
        $definition = $container->getDefinition($serviceClassName);

        foreach ($taggedServices as $taggedServiceId => $tags)
        {
            $definition->addMethodCall($method, array(new Reference($taggedServiceId)));
        }
    }
}
