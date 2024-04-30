<?php

namespace Chamilo\Core\API\Service\Architecture\Routing;

use Symfony\Component\Routing\RouteCollection;

class APIRoutingLoader
{
    protected array $apiRoutingExtensions;

    public function addLoaderExtension(APIRoutingLoaderExtensionInterface $extension): void
    {
        $this->apiRoutingExtensions[] = $extension;
    }

    public function loadRoutes(): RouteCollection
    {
        $routeCollection = new RouteCollection();

        foreach($this->apiRoutingExtensions as $extension)
        {
            $extension->load($routeCollection);
        }

        return $routeCollection;
    }
}