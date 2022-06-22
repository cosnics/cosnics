<?php
namespace Chamilo\Libraries\Architecture\Application\Routing;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * This service will be used to generate urls using the current url as a base
 *
 * @package Chamilo\Libraries\Architecture\Application\Routing
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UrlGenerator
{

    private ChamiloRequest $request;

    private ParameterBag $urlParameterBag;

    public function __construct(ChamiloRequest $request)
    {
        $this->setUrlParameterBag($request->query);
        $this->request = $request;
    }

    /**
     * Shortcut for the url generator to generate a url for a given context and component
     */
    public function generateContextURL(string $context, string $component, array $parameters = [], array $filters = []
    ): string
    {
        if ($context)
        {
            $parameters[Application::PARAM_CONTEXT] = $context;
        }

        if ($component)
        {
            $parameters[Application::PARAM_ACTION] = $component;
        }

        return $this->generateURL($parameters, $filters);
    }

    /**
     * Generates a url based on the current url from the request, with the given parameters and filters
     */
    public function generateURL(array $parameters = [], array $filters = []): string
    {
        $baseParameters = $this->urlParameterBag->all();
        $this->urlParameterBag->add($parameters);

        foreach ($filters as $filter)
        {
            $this->urlParameterBag->remove($filter);
        }

        $parameters = $this->urlParameterBag->all();
        $parametersUrlString = count($parameters) ? '?' . urldecode(http_build_query($parameters)) : '';

        $this->urlParameterBag->replace($baseParameters);

        $request = $this->getRequest();
        $basePath = $request->getSchemeAndHttpHost() . $request->getBaseUrl() . $request->getPathInfo();

        return $basePath . $parametersUrlString;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function setUrlParameterBag(ParameterBag $urlParameterBag)
    {
        $this->urlParameterBag = $urlParameterBag;
    }
}