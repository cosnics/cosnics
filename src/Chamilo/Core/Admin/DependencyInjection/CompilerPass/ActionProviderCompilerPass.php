<?php
namespace Chamilo\Core\Admin\DependencyInjection\CompilerPass;

use Chamilo\Core\Admin\Architecture\Domain\ActionProviderCollection;
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

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(ActionProviderCollection::class))
        {
            $taggedServices = $container->findTaggedServiceIds(ActionProviderInterface::class);

            $definition = $container->getDefinition(ActionProviderCollection::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addActionProvider', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
