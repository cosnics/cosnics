<?php
namespace Chamilo\Core\Repository\DependencyInjection\CompilerPass;

use Chamilo\Core\Repository\Publication\Service\PublicationTargetProcessor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to collect the integration PublicationModifier objects
 *
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetProcessorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(PublicationTargetProcessor::class))
        {
            $taggedServices = $container->findTaggedServiceIds(
                'Chamilo\Core\Repository\Publication\Service\PublicationModifier'
            );

            $definition = $container->getDefinition(PublicationTargetProcessor::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall(
                    'addPublicationModifier', [new Reference($taggedServiceId)]
                );
            }
        }
    }
}
