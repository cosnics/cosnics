<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Bootstrap\ApplicationFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to add new FormTypes as services to symfony forms
 *
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ApplicationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(ApplicationFactory::class)) {
            $taggedServices = $container->findTaggedServiceIds(ApplicationInterface::class);
            $definition = $container->getDefinition(ApplicationFactory::class);

            foreach ($taggedServices as $taggedServiceId => $tags) {
                $definition->addMethodCall('addApplicationComponent', [new Reference($taggedServiceId)]);
            }
        }
    }
}