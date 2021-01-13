<?php

namespace Chamilo\Application\ExamAssignment\DependencyInjection\CompilerPass;

use Chamilo\Application\ExamAssignment\Service\Kernel\RequestValidator;
use Chamilo\Application\ExamAssignment\Service\Kernel\RequestValidatorExtensionInterface;
use Chamilo\Libraries\DependencyInjection\CompilerPass\TaggedServicesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass to collect request validator extensions
 *
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RequestValidatorCompilerPass extends TaggedServicesCompilerPass
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addTaggedServicesToService(
            $container, RequestValidator::class, RequestValidatorExtensionInterface::class,
            'addRequestValidatorExtension'
        );
    }
}
