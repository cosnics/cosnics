<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Storage\Service\ConditionPartTranslator;
use Chamilo\Libraries\Storage\Service\ConditionPartTranslatorService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineConditionPartTranslatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(ConditionPartTranslatorService::class))
        {
            $taggedServices = $container->findTaggedServiceIds(ConditionPartTranslator::class);
            $definition = $container->getDefinition(ConditionPartTranslatorService::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addConditionPartTranslator', [new Reference($taggedServiceId)]);
            }
        }
    }
}