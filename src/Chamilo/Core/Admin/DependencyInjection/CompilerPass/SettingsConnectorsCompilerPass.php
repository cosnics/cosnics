<?php
namespace Chamilo\Core\Admin\DependencyInjection\CompilerPass;

use Chamilo\Core\Admin\Architecture\Domain\SettingsConnectorRegistry;
use Chamilo\Core\Admin\Architecture\Interface\SettingsConnectorInterface;
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

    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(SettingsConnectorRegistry::class))
        {
            $taggedServices = $container->findTaggedServiceIds(SettingsConnectorInterface::class);

            $definition = $container->getDefinition(SettingsConnectorRegistry::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addSettingsConnector', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
