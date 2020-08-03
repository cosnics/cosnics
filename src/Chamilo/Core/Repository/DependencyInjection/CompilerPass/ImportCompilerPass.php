<?php

namespace Chamilo\Core\Repository\DependencyInjection\CompilerPass;

use Chamilo\Core\Repository\Common\Import\Factory\ImportFactories;
use Chamilo\Core\Repository\Common\Import\Factory\ImportFactoryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to collect the import factories
 *
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ImportCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(ImportFactories::class))
        {
            $taggedServices = $container->findTaggedServiceIds(ImportFactoryInterface::class);
            $definition = $container->getDefinition(ImportFactories::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                foreach ($tags as $attributes)
                {
                    $definition->addMethodCall(
                        'addImportFactory',
                        array($attributes['alias'], new Reference($taggedServiceId))
                    );
                }
            }
        }
    }
}
