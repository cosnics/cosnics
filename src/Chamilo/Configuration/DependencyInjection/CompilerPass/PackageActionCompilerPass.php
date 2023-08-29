<?php
namespace Chamilo\Configuration\DependencyInjection\CompilerPass;

use Chamilo\Configuration\Package\Action\Activator;
use Chamilo\Configuration\Package\Action\Deactivator;
use Chamilo\Configuration\Package\Action\Installer;
use Chamilo\Configuration\Package\Action\PackageActionFactory;
use Chamilo\Configuration\Package\Action\Remover;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Configuration\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PackageActionCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(PackageActionFactory::class))
        {
            $definition = $container->getDefinition(PackageActionFactory::class);

            $taggedInstallerServices = $container->findTaggedServiceIds(Installer::class);

            foreach ($taggedInstallerServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addPackageInstaller', [new Reference($taggedServiceId)]
                );
            }

            $taggedRemoverServices = $container->findTaggedServiceIds(Remover::class);

            foreach ($taggedRemoverServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addPackageRemover', [new Reference($taggedServiceId)]
                );
            }

            $taggedActivatorServices = $container->findTaggedServiceIds(Activator::class);

            foreach ($taggedActivatorServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addPackageActivator', [new Reference($taggedServiceId)]
                );
            }

            $taggedDeactivatorServices = $container->findTaggedServiceIds(Deactivator::class);

            foreach ($taggedDeactivatorServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addPackageDeactivator', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
