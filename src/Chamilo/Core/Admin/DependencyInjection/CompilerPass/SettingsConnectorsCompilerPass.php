<?php
namespace Chamilo\Core\Admin\DependencyInjection\CompilerPass;

use Chamilo\Core\Admin\Architecture\Domain\SettingsConnectorCollection;
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

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(SettingsConnectorCollection::class))
        {
            $taggedServices = $container->findTaggedServiceIds(SettingsConnectorInterface::class);

            $definition = $container->getDefinition(SettingsConnectorCollection::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addSettingsConnector', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
