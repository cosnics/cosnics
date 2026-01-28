<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EventDispatcherCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container): void
    {
        if ($container->has(EventDispatcherInterface::class))
        {
            $taggedServices = $container->findTaggedServiceIds(EventSubscriberInterface::class);

            $definition = $container->findDefinition(EventDispatcherInterface::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addSubscriber', [new Reference($taggedServiceId)]);
            }
        }
    }
}