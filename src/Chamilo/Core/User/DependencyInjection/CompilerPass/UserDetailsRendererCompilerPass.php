<?php
namespace Chamilo\Core\User\DependencyInjection\CompilerPass;

use Chamilo\Core\User\Architecture\Interfaces\UserDetailsRendererInterface;
use Chamilo\Core\User\Domain\UserDetails\UserDetailsRendererCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\User\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserDetailsRendererCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(UserDetailsRendererCollection::class))
        {
            $taggedServices = $container->findTaggedServiceIds(UserDetailsRendererInterface::class);

            $definition = $container->getDefinition(UserDetailsRendererCollection::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addUserDetailsRenderer', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
