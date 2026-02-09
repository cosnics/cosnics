<?php
namespace Chamilo\Core\User\DependencyInjection\CompilerPass;

use Chamilo\Core\User\Architecture\Domain\UserDetailsRendererRegistry;
use Chamilo\Core\User\Architecture\Interface\UserDetailsRendererInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\User\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserDetailsRendererCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(UserDetailsRendererRegistry::class))
        {
            $taggedServices = $container->findTaggedServiceIds(UserDetailsRendererInterface::class);

            $definition = $container->getDefinition(UserDetailsRendererRegistry::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addUserDetailsRenderer', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
