<?php
namespace Chamilo\Core\Home\DependencyInjection\CompilerPass;

use Chamilo\Core\Home\Renderer\BlockRendererFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\Home\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AvailableBlockRendererCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(BlockRendererFactory::class))
        {
            $taggedServices = $container->findTaggedServiceIds(
                'Chamilo\Core\Home\Renderer\BlockRenderer'
            );

            $definition = $container->getDefinition(BlockRendererFactory::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addAvailableBlockRenderer', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
