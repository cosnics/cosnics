<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConsoleCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('Chamilo\Libraries\Protocol\Console\Console')) {
            $taggedServices = $container->findTaggedServiceIds(Command::class);
            $consoleDefinition = $container->getDefinition('Chamilo\Libraries\Protocol\Console\Console');

            foreach ($taggedServices as $taggedServiceId => $tags) {
                $consoleDefinition->addMethodCall('add', [new Reference($taggedServiceId)]);
            }
        }

        if ($container->hasDefinition('Chamilo\Libraries\Protocol\Console\HelperSet')) {
            $taggedServices = $container->findTaggedServiceIds('chamilo.libraries.console.helper');
            $helperSetDefinition = $container->getDefinition('Chamilo\Libraries\Protocol\Console\HelperSet');

            foreach ($taggedServices as $taggedServiceId => $tags) {
                $helperSetDefinition->addMethodCall('set', [new Reference($taggedServiceId), $tags[0]['alias']]);
            }
        }
    }
}