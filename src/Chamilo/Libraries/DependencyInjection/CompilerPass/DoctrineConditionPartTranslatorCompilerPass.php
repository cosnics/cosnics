<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Storage\DataManager\Doctrine\Service\ConditionPartTranslatorService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to add new FormTypes as services to symfony forms
 *
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class DoctrineConditionPartTranslatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(ConditionPartTranslatorService::class))
        {
            $taggedServices = $container->findTaggedServiceIds(ConditionPartTranslatorService::class);
            $definition = $container->getDefinition(ConditionPartTranslatorService::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addConditionPartTranslator', [new Reference($taggedServiceId)]);
            }
        }
    }
}