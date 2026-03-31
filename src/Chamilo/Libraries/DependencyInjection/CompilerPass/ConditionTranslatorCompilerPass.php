<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Storage\Architecture\Domain\ConditionTranslatorRegistry;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionTranslatorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ConditionTranslatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(ConditionTranslatorRegistry::class)) {
            $taggedServices = $container->findTaggedServiceIds(ConditionTranslatorInterface::class);
            $definition = $container->getDefinition(ConditionTranslatorRegistry::class);

            foreach ($taggedServices as $taggedServiceId => $tags) {
                $definition->addMethodCall('addConditionTranslator', [new Reference($taggedServiceId)]);
            }
        }
    }
}