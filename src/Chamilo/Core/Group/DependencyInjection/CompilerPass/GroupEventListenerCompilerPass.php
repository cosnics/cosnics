<?php
namespace Chamilo\Core\Group\DependencyInjection\CompilerPass;

use Chamilo\Core\Group\Service\GroupEventListenerInterface;
use Chamilo\Core\Group\Service\GroupEventNotifier;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\Group\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GroupEventListenerCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(GroupEventNotifier::class))
        {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds(GroupEventListenerInterface::class);

        $definition = $container->getDefinition(GroupEventNotifier::class);

        foreach ($taggedServices as $taggedServiceId => $tags)
        {
            $definition->addMethodCall(
                'addGroupEventListener', array(new Reference($taggedServiceId))
            );
        }
    }
}