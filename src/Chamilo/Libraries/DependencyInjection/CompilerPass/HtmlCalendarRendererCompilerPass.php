<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Calendar\Factory\HtmlCalendarRendererFactory;
use Chamilo\Libraries\Calendar\Service\View\HtmlCalendarRenderer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlCalendarRendererCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(HtmlCalendarRendererFactory::class)) {
            $taggedServices = $container->findTaggedServiceIds(HtmlCalendarRenderer::class);
            $definition = $container->getDefinition(HtmlCalendarRendererFactory::class);

            foreach ($taggedServices as $taggedServiceId => $tags) {
                $definition->addMethodCall('addHtmlCalendarRenderer', [new Reference($taggedServiceId)]);
            }
        }
    }
}