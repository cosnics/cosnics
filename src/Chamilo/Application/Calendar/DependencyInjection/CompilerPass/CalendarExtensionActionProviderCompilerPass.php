<?php
namespace Chamilo\Application\Calendar\DependencyInjection\CompilerPass;

use Chamilo\Application\Calendar\Architecture\Domain\CalendarExtensionActionProviderCollection;
use Chamilo\Application\Calendar\Architecture\Interface\CalendarExtensionActionProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Application\Calendar\DependencyInjection\CompilerPass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CalendarExtensionActionProviderCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(CalendarExtensionActionProviderCollection::class))
        {
            $definition = $container->getDefinition(CalendarExtensionActionProviderCollection::class);

            $taggedServices = $container->findTaggedServiceIds(CalendarExtensionActionProviderInterface::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addCalendarExtenstionActionProvider', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
