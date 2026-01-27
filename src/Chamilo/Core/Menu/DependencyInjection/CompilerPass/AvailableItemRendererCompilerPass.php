<?php
namespace Chamilo\Core\Menu\DependencyInjection\CompilerPass;

use Chamilo\Core\Menu\Architecture\Domain\ItemRendererCollection;
use Chamilo\Core\Menu\UserInterface\MenuRenderer\ItemRenderer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\Menu\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AvailableItemRendererCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(ItemRendererCollection::class))
        {
            $taggedServices = $container->findTaggedServiceIds(ItemRenderer::class);

            $definition = $container->getDefinition(ItemRendererCollection::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addItemRenderer', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
