<?php

namespace Chamilo\Application\Plagiarism\DependencyInjection\CompilerPass;

use Chamilo\Application\Plagiarism\Service\Turnitin\Events\TurnitinEventListenerInterface;
use Chamilo\Application\Plagiarism\Service\Turnitin\Events\TurnitinEventNotifier;
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
class TurnitinEventListenerCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(TurnitinEventNotifier::class))
        {
            $taggedServices = $container->findTaggedServiceIds(TurnitinEventListenerInterface::class);
            $definition =  $container->getDefinition(TurnitinEventNotifier::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addEventListener', array(new Reference($taggedServiceId)));
            }
        }
    }
}
