<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Storage\Architecture\Domain\ConditionVariableTranslatorCollection;
use Chamilo\Libraries\Storage\Architecture\Interface\ConditionVariableTranslatorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ConditionVariableTranslatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(ConditionVariableTranslatorCollection::class)) {
            $taggedServices = $container->findTaggedServiceIds(ConditionVariableTranslatorInterface::class);
            $definition = $container->getDefinition(ConditionVariableTranslatorCollection::class);

            foreach ($taggedServices as $taggedServiceId => $tags) {
                $definition->addMethodCall('addConditionVariableTranslator', [new Reference($taggedServiceId)]);
            }
        }
    }
}