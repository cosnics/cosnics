<?php

namespace Chamilo\Core\API\Service\Architecture\Routing;

use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class APIRouteMatcher
{
    protected APIRoutingLoader $routeLoader;
    protected RouteCollection $routes;

    public function __construct(APIRoutingLoader $routeLoader)
    {
        $this->routeLoader = $routeLoader;

        $this->routes = $this->routeLoader->loadRoutes();
    }

    public function match(Request $request): array
    {
        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($this->routes, $context);

        return $matcher->match($request->getPathInfo());
    }

}