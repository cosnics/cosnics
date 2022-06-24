<?php

namespace Chamilo\Core\Admin\DependencyInjection\CompilerPass;

use Chamilo\Core\Admin\Service\ActionProvider;
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

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(ActionProvider::class))
        {
            $taggedServices = $container->findTaggedServiceIds(
                'Chamilo\Core\Admin\Service\ActionProvider'
            );

            $definition = $container->getDefinition(ActionProvider::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addActionProvider', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
