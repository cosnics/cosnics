<?php
namespace Chamilo\Application\Calendar\DependencyInjection\CompilerPass;

use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionDataProviderCollection;
use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionDataProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Application\Calendar\DependencyInjection\CompilerPass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarExtensionDataProviderCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(CalendarExtensionDataProviderCollection::class))
        {
            $definition = $container->getDefinition(CalendarExtensionDataProviderCollection::class);

            $taggedServices = $container->findTaggedServiceIds(CalendarExtensionDataProviderInterface::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addCalendarExtensionDataProvider', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
