<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Cache\CacheManagement\CacheDataPreLoaderManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to add CacheServiceConstructorInterfaces objects to the CacheManagerBuilder
 *
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CacheDataPreLoaderCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(CacheDataPreLoaderManager::class))
        {
            $taggedServices = $container->findTaggedServiceIds('Chamilo\Libraries\Cache\CacheDataPreLoaderService');

            $definition = $container->getDefinition(CacheDataPreLoaderManager::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addCacheDataPreLoaderService', [$taggedServiceId, new Reference($taggedServiceId)]
                );
            }
        }
    }
}