<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\DependencyInjection\CompilerPass;

use Chamilo\Application\ExamAssignment\Service\Kernel\RequestValidator;
use Chamilo\Application\ExamAssignment\Service\Kernel\RequestValidatorExtensionInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Extension\HomeRendererExtensionInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer\Extension\HomeRendererExtensionManager;
use Chamilo\Libraries\DependencyInjection\CompilerPass\TaggedServicesCompilerPass;
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
class HomeExtensionCompilerPass extends TaggedServicesCompilerPass
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addTaggedServicesToService(
            $container, HomeRendererExtensionManager::class, HomeRendererExtensionInterface::class,
            'addHomeRendererExtension'
        );
    }
}
