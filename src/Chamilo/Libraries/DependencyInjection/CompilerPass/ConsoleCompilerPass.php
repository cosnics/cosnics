<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to add commands to the console runner
 *
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConsoleCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('chamilo.libraries.console'))
        {
            $taggedServices = $container->findTaggedServiceIds('chamilo.libraries.console.command');
            $consoleDefinition = $container->getDefinition('chamilo.libraries.console');

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $consoleDefinition->addMethodCall('add', array(new Reference($taggedServiceId)));
            }
        }

        if ($container->hasDefinition('chamilo.libraries.console.helper_set'))
        {
            $taggedServices = $container->findTaggedServiceIds('chamilo.libraries.console.helper');
            $helperSetDefinition = $container->getDefinition('chamilo.libraries.console.helper_set');

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $helperSetDefinition->addMethodCall('set', array(new Reference($taggedServiceId), $tags[0]['alias']));
            }
        }
    }
}