<?php

namespace Chamilo\Application\ExamAssignment\DependencyInjection\CompilerPass;

use Chamilo\Application\ExamAssignment\Service\Kernel\RequestValidator;
use Chamilo\Application\ExamAssignment\Service\Kernel\RequestValidatorExtensionInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to collect request validator extensions
 *
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RequestValidatorCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(RequestValidator::class))
        {
            $taggedServices = $container->findTaggedServiceIds(RequestValidatorExtensionInterface::class);
            $definition =  $container->getDefinition(RequestValidator::class);

            foreach ($taggedServices as $taggedServiceId => $tags)
            {
                $definition->addMethodCall('addRequestValidatorExtension', array(new Reference($taggedServiceId)));
            }
        }
    }
}
