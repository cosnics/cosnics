<?php

namespace Chamilo\Core\API\DependencyInjection\CompilerPass;

use Chamilo\Application\Plagiarism\Events\PlagiarismEventDispatcher;
use Chamilo\Application\Plagiarism\Events\PlagiarismEventSubscriber;
use Chamilo\Core\API\Service\Architecture\Routing\APIRoutingLoaderExtensionInterface;
use Chamilo\Core\API\Service\Architecture\Routing\APIRoutingLoader;
use Chamilo\Libraries\DependencyInjection\CompilerPass\TaggedServicesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class APIRoutingLoaderExtensionCompilerPass extends TaggedServicesCompilerPass
{
    public function process(ContainerBuilder $container): void
    {
        $this->addTaggedServicesToService(
            $container, APIRoutingLoader::class,
            APIRoutingLoaderExtensionInterface::class, 'addLoaderExtension'
        );
    }
}