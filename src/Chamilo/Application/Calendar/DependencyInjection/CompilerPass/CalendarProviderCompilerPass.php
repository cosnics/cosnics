<?php
namespace Chamilo\Application\Calendar\DependencyInjection\CompilerPass;

use Chamilo\Application\Calendar\Service\CalendarProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Application\Calendar\DependencyInjection\CompilerPass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarProviderCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(CalendarProvider::class))
        {
            $definition = $container->getDefinition(CalendarProvider::class);

            $taggedServices = $container->findTaggedServiceIds(
                'Chamilo\Application\Calendar\Architecture\CalendarDataProviderInterface'
            );

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addCalendarDataProvider', [new Reference($taggedServiceId)]
                );
            }

            $taggedServices = $container->findTaggedServiceIds(
                'Chamilo\Application\Calendar\Architecture\ActionsProviderInterface'
            );

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addActionsProvider', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
