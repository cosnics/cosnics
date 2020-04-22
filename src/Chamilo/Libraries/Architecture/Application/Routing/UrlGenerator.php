<?php
namespace Chamilo\Libraries\Architecture\Application\Routing;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\ChamiloRequest;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * This service will be used to generate urls using the current url as a base
 *
 * @package Chamilo\Libraries\Architecture\Application\Routing
 * @author Sven
 */
class UrlGenerator
{

    /**
     * The parameters of the url in a parameter bag
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    private $urlParameterBag;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function __construct(ChamiloRequest $request)
    {
        $this->setUrlParameterBag($request->query);
    }

    /**
     * Shortcut for the url generator to generate a url for a given context and component
     *
     * @param string $context
     * @param string $component
     * @param string[] $parameters
     * @param string[] $filters
     *
     * @return string
     */
    public function generateContextURL($context, $component, $parameters = array(), $filters = array())
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
     *
     * @param string[] $parameters - This array must be used to define new or update existing parameters
     * @param string[] $filters - This array must be used to filter out parameters from the current url
     *
     * @return string
     */
    public function generateURL($parameters = array(), $filters = array())
    {
        $baseParameters = $this->urlParameterBag->all();
        $this->urlParameterBag->add($parameters);

        foreach ($filters as $filter)
        {
            $this->urlParameterBag->remove($filter);
        }

        $parameters = $this->urlParameterBag->all();
        $parameters_url_string = count($parameters) ? '?' . urldecode(http_build_query($parameters)) : '';

        $this->urlParameterBag->replace($baseParameters);

        return 'index.php' . $parameters_url_string;
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\ParameterBag $urlParameterBag
     *
     * @throws \InvalidArgumentException
     */
    public function setUrlParameterBag($urlParameterBag)
    {
        if (!$urlParameterBag instanceof ParameterBag)
        {
            throw new InvalidArgumentException(
                'The given url parameter bag is not an instance of "\Symfony\Component\HttpFoundation\ParameterBag", ' .
                'instead "' . get_class($urlParameterBag) . '" was given.'
            );
        }

        $this->urlParameterBag = $urlParameterBag;
    }
}