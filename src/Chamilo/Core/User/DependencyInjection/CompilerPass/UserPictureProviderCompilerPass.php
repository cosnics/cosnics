<?php
namespace Chamilo\Core\User\DependencyInjection\CompilerPass;

use Chamilo\Core\User\Architecture\Domain\UserPictureProviderRegistry;
use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\User\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserPictureProviderCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(UserPictureProviderRegistry::class))
        {
            $taggedServices = $container->findTaggedServiceIds(UserPictureProviderInterface::class);

            $definition = $container->getDefinition(UserPictureProviderRegistry::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addAvailablePictureProvider', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
