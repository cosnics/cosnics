<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Domain\UserExceptionRendererRegistry;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionRendererInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserExceptionRendererCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(UserExceptionRendererRegistry::class)) {
            $taggedServices = $container->findTaggedServiceIds(UserExceptionRendererInterface::class);
            $definition = $container->getDefinition(UserExceptionRendererRegistry::class);

            foreach ($taggedServices as $taggedServiceId => $tags) {
                $definition->addMethodCall('addUserExceptionRenderer', [new Reference($taggedServiceId)]);
            }
        }
    }
}