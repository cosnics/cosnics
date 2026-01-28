<?php
namespace Chamilo\Libraries\DependencyInjection\CompilerPass;

use Chamilo\Libraries\Protocol\Security\Factory\HashingAlgorithmFactory;
use Chamilo\Libraries\Protocol\Security\Service\HashingAlgorithm;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @package Chamilo\Libraries\DependencyInjection\CompilerPass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HashingCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(HashingAlgorithmFactory::class))
        {
            $taggedServices = $container->findTaggedServiceIds(HashingAlgorithm::class);
            $definition = $container->getDefinition(HashingAlgorithmFactory::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addHashingAlgorithm', [new Reference($taggedServiceId)]);
            }
        }
    }
}