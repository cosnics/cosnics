<?php
namespace Chamilo\Core\User\DependencyInjection\CompilerPass;

use Chamilo\Core\User\Picture\UserPictureProviderFactory;
use Chamilo\Core\User\Picture\UserPictureProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\User\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserPictureProviderCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(UserPictureProviderFactory::class))
        {
            $taggedServices = $container->findTaggedServiceIds(UserPictureProviderInterface::class);

            $definition = $container->getDefinition(UserPictureProviderFactory::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addAvailablePictureProvider', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
