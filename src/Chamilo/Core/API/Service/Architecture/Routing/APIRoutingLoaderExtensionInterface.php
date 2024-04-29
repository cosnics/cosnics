<?php

namespace Chamilo\Core\API\Service\Architecture\Routing;

use Symfony\Component\Routing\RouteCollection;

interface APIRoutingLoaderExtensionInterface
{
    public function load(RouteCollection $collection): void;
}