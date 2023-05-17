<?php
namespace Chamilo\Libraries\Architecture\Application\Routing;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * This service will be used to generate urls using the current url as a base
 *
 * @package Chamilo\Libraries\Architecture\Application\Routing
 * @author  Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UrlGenerator
{

    private ChamiloRequest $request;

    private WebPathBuilder $webPathBuilder;

    public function __construct(ChamiloRequest $request, WebPathBuilder $webPathBuilder)
    {
        $this->request = $request;
        $this->webPathBuilder = $webPathBuilder;
    }

    /**
     * Shortcut for the url generator to generate a url for a given context and component
     */
    public function forContext(
        string $context, string $component, array $parameters = [], array $filters = [], ?string $anchor = null
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

        return $this->fromRequest($parameters, $filters, $anchor);
    }

    public function fromParameters(array $parameters = [], array $filters = [], ?string $anchor = null): string
    {
        return $this->generate(new ParameterBag(), $parameters, $filters, $anchor);
    }

    /**
     * Generates a url based on the current url from the request, with the given parameters and filters
     */
    public function fromRequest(array $parameters = [], array $filters = [], ?string $anchor = null): string
    {
        return $this->generate(new ParameterBag($this->getRequest()->query->all()), $parameters, $filters, $anchor);
    }

    protected function generate(
        ParameterBag $parameterBag, array $parameters = [], array $filters = [], ?string $anchor = null
    ): string
    {
        $parameterBag->add($parameters);

        foreach ($filters as $filter)
        {
            $parameterBag->remove($filter);
        }

        $urlParts = [];

        $urlParts[] = $this->getWebPathBuilder()->getBasePath();

        if ($parameterBag->count())
        {
            $urlParts[] = '?' . urldecode(http_build_query($parameterBag->all()));
        }

        if ($anchor)
        {
            $urlParts[] = '#' . $anchor;
        }

        return implode('', $urlParts);
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }
}