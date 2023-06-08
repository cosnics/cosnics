<?php
namespace Chamilo\Core\Home\DependencyInjection\CompilerPass;

use Chamilo\Core\Home\Service\AngularConnectorService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\Home\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class AngularConnectorCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(AngularConnectorService::class))
        {
            $taggedServices = $container->findTaggedServiceIds(
                AngularConnectorService::class
            );

            $definition = $container->getDefinition(AngularConnectorService::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addAngularConnector', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
