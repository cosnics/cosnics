<?php
namespace Chamilo\Core\Admin\DependencyInjection\CompilerPass;

use Chamilo\Core\Admin\Architecture\Domain\ActionProviderRegistry;
use Chamilo\Core\Admin\Architecture\Interface\ActionProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\Admin\DependencyInjection\CompilerPass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ActionProviderCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(ActionProviderRegistry::class))
        {
            $taggedServices = $container->findTaggedServiceIds(ActionProviderInterface::class);

            $definition = $container->getDefinition(ActionProviderRegistry::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addActionProvider', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
