<?php

namespace Chamilo\Application\Plagiarism\DependencyInjection\CompilerPass;

use Chamilo\Application\Plagiarism\Service\Events\PlagiarismEventListenerInterface;
use Chamilo\Application\Plagiarism\Service\Events\PlagiarismEventNotifier;
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
class PlagiarismEventListenerCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(PlagiarismEventNotifier::class))
        {
            $taggedServices = $container->findTaggedServiceIds(PlagiarismEventListenerInterface::class);
            $definition =  $container->getDefinition(PlagiarismEventNotifier::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addEventListener', array(new Reference($taggedServiceId)));
            }
        }
    }
}
