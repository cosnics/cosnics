<?php

namespace Chamilo\Core\User\DependencyInjection\CompilerPass;

use Chamilo\Core\User\Service\UserPropertiesExtension\UserPropertiesExtensionInterface;
use Chamilo\Core\User\Service\UserPropertiesExtension\UserPropertiesExtensionManager;
use Chamilo\Libraries\DependencyInjection\CompilerPass\TaggedServicesCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass to collect the import factories
 *
 * @package Chamilo\Core\Repository\DependencyInjection\CompilerPass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserPropertiesExtensionCompilerPass extends TaggedServicesCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $this->addTaggedServicesToService(
            $container, UserPropertiesExtensionManager::class, UserPropertiesExtensionInterface::class,
            'addUserPropertiesExtension'
        );
    }
}
