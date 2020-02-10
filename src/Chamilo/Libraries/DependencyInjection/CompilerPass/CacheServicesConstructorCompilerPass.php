<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Cache\CacheManagement\CacheManagerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to add CacheServiceConstructorInterfaces objects to the CacheManagerBuilder
 *
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CacheServicesConstructorCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('chamilo.libraries.cache.cache_management.cache_manager_builder'))
        {
            $taggedServices = $container->findTaggedServiceIds(
                'chamilo.libraries.cache.cache_management.cache_services_constructor'
            );

            $consoleDefinition = $container->getDefinition(CacheManagerBuilder::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $consoleDefinition->addMethodCall('addCacheServiceConstructor', array(new Reference($taggedServiceId)));
            }
        }
    }
}