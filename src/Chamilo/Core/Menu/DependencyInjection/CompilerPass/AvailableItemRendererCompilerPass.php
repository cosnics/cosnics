<?php
namespace Chamilo\Core\Menu\DependencyInjection\CompilerPass;

use Chamilo\Core\Menu\Factory\ItemRendererFactory;
use Chamilo\Core\Menu\Service\Renderer\ItemRenderer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\Menu\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AvailableItemRendererCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(ItemRendererFactory::class))
        {
            $taggedServices = $container->findTaggedServiceIds(ItemRenderer::class);

            $definition = $container->getDefinition(ItemRendererFactory::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addAvailableItemRenderer', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
