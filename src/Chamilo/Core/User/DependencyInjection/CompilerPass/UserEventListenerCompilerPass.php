<?php
namespace Chamilo\Core\User\DependencyInjection\CompilerPass;

use Chamilo\Core\User\Service\UserEventListenerInterface;
use Chamilo\Core\User\Service\UserEventNotifier;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\User\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserEventListenerCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(UserEventNotifier::class))
        {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds(UserEventListenerInterface::class);

        $definition = $container->getDefinition(UserEventNotifier::class);

        foreach ($taggedServices as $taggedServiceId => $tags)
        {
            $definition->addMethodCall(
                'addUserEventListener', [new Reference($taggedServiceId)]
            );
        }
    }
}