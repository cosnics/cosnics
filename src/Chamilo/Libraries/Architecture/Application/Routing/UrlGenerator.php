<?php

namespace Chamilo\Libraries\Architecture\Application\Routing;

use Chamilo\Libraries\Architecture\Application\Application;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * This service will be used to generate urls using the current url as a base
 *
 * @author Sven
 */
class UrlGenerator
{
    /**
     * The parameters of the url in a parameter bag
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    private $url_parameter_bag;

    /**
     * Constructor
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->setUrlParameterBag($request->query);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\ParameterBag $url_parameter_bag
     *
     * @throws \InvalidArgumentException
     */
    public function setUrlParameterBag($url_parameter_bag)
    {
        if(!$url_parameter_bag instanceof ParameterBag)
        {
            throw new \InvalidArgumentException(
                'The given url parameter bag is not an instance of "\Symfony\Component\HttpFoundation\ParameterBag", ' .
                'instead "' . get_class($url_parameter_bag) . '" was given.'
            );
        }

        $this->url_parameter_bag = $url_parameter_bag;
    }

    /**
     * Generates a url based on the current url from the request, with the given parameters and filters
     *
     * @param array $parameters - This array must be used to define new or update existing parameters
     * @param array $filters - This array must be used to filter out parameters from the current url
     *
     * @return string
     */
    public function generateURL($parameters = array(), $filters = array())
    {
        $base_parameters = $this->url_parameter_bag->all();
        $this->url_parameter_bag->add($parameters);

        foreach($filters as $filter)
        {
            $this->url_parameter_bag->remove($filter);
        }

        $parameters = $this->url_parameter_bag->all();
        $parameters_url_string = count($parameters) ? '?' . urldecode(http_build_query($parameters)) : '';

        $this->url_parameter_bag->replace($base_parameters);

        return 'index.php' . $parameters_url_string;
    }

    /**
     * Shortcut for the url generator to generate a url for a given context and component
     *
     * @param string $context
     * @param string $component
     * @param array $parameters
     * @param array $filters
     *
     * @return string
     */
    public function generateContextURL($context, $component, $parameters = array(), $filters = array())
    {
        if($context)
        {
            $parameters[Application::PARAM_CONTEXT] = $context;
        }

        if($component)
        {
            $parameters[Application::PARAM_ACTION] = $component;
        }

        return $this->generateURL($parameters, $filters);
    }
}