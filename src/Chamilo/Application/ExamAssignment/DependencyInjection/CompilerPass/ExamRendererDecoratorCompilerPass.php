<?php

namespace Chamilo\Application\ExamAssignment\DependencyInjection\CompilerPass;

use Chamilo\Application\ExamAssignment\Service\Decorator\ExamRendererDecoratorInterface;
use Chamilo\Application\ExamAssignment\Service\Decorator\ExamRendererDecoratorManager;
use Chamilo\Libraries\DependencyInjection\CompilerPass\TaggedServicesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass to collect request validator extensions
 *
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExamRendererDecoratorCompilerPass extends TaggedServicesCompilerPass
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addTaggedServicesToService(
            $container, ExamRendererDecoratorManager::class, ExamRendererDecoratorInterface::class,
            'addExamRendererDecorator'
        );
    }
}
