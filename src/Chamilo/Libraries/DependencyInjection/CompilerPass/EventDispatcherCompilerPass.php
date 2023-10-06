<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EventDispatcherCompilerPass implements CompilerPassInterface
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
        if ($container->has(EventDispatcherInterface::class))
        {
            $taggedServices = $container->findTaggedServiceIds('Chamilo\Libraries\EventDispatcher\Subscriber');

            $definition = $container->findDefinition(EventDispatcherInterface::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addSubscriber', [new Reference($taggedServiceId)]);
            }
        }
    }
}