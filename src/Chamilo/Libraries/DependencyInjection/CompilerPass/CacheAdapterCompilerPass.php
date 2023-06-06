<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Cache\CacheManagement\SymfonyCacheAdapterManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CacheAdapterCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(SymfonyCacheAdapterManager::class))
        {
            $taggedServices = $container->findTaggedServiceIds('Symfony\Component\Cache\Adapter');

            $definition = $container->getDefinition(SymfonyCacheAdapterManager::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addCacheAdapter', [$taggedServiceId, new Reference($taggedServiceId)]);
            }
        }
    }
}