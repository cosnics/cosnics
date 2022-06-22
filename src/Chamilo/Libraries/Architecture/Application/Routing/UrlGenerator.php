<?php
namespace Chamilo\Libraries\Architecture\Application\Routing;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\PathBuilder;
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

    private PathBuilder $pathBuilder;

    private ChamiloRequest $request;

    private ParameterBag $urlParameterBag;

    public function __construct(ChamiloRequest $request, PathBuilder $pathBuilder)
    {
        $this->request = $request;
        $this->setUrlParameterBag($request->query);
        $this->pathBuilder = $pathBuilder;
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

        return $this->getPathBuilder()->getBasePath() . $parametersUrlString;
    }

    public function getPathBuilder(): PathBuilder
    {
        return $this->pathBuilder;
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