<?php

namespace Chamilo\Application\Plagiarism\DependencyInjection\CompilerPass;

use Chamilo\Application\Plagiarism\Service\UserConverter\UserConverterFactory;
use Chamilo\Application\Plagiarism\Service\UserConverter\UserConverterInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to collect the event listeners for turnitin
 *
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserConverterCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(UserConverterFactory::class))
        {
            $taggedServices = $container->findTaggedServiceIds(UserConverterInterface::class);
            $definition =  $container->getDefinition(UserConverterFactory::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addUserConverter', array(new Reference($taggedServiceId)));
            }
        }
    }
}
