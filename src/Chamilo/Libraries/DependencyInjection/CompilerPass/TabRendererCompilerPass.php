<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabRendererRegistry;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Interface\TabRendererInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TabRendererCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(TabRendererRegistry::class)) {
            $taggedServices = $container->findTaggedServiceIds(TabRendererInterface::class);
            $definition = $container->getDefinition(TabRendererRegistry::class);

            foreach ($taggedServices as $taggedServiceId => $tags) {
                $definition->addMethodCall('addTabRenderer', [new Reference($taggedServiceId)]);
            }
        }
    }
}