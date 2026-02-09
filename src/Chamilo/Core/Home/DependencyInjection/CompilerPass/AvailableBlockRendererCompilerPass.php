<?php
namespace Chamilo\Core\Home\DependencyInjection\CompilerPass;

use Chamilo\Core\Home\Architecture\Domain\BlockRendererRegistry;
use Chamilo\Core\Home\UserInterface\HomeRenderer\BlockRenderer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\Home\DependencyInjection\CompilerPass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AvailableBlockRendererCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(BlockRendererRegistry::class))
        {
            $taggedServices = $container->findTaggedServiceIds(BlockRenderer::class);

            $definition = $container->getDefinition(BlockRendererRegistry::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addBlockRenderer', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
