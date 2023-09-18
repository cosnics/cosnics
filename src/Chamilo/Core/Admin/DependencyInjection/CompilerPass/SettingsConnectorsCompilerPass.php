<?php
namespace Chamilo\Core\Admin\DependencyInjection\CompilerPass;

use Chamilo\Core\Admin\Service\ActionProvider;
use Chamilo\Core\Admin\Service\SettingsConnectorFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Core\Admin\DependencyInjection\CompilerPass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SettingsConnectorsCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(SettingsConnectorFactory::class))
        {
            $taggedServices = $container->findTaggedServiceIds(
                'Chamilo\Core\Admin\Service\SettingsConnectorInterface'
            );

            $definition = $container->getDefinition(SettingsConnectorFactory::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addSettingsConnector', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
