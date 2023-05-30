<?php
namespace Chamilo\Core\Home\Renderer;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;

/**
 * @package Chamilo\Core\Home\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Renderer
{
    public const TYPE_BASIC = 'Basic';

    /**
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return string
     */
    abstract public function render();

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(UrlGenerator::class);
    }

    /**
     * Gets a link to the personal calendar application
     *
     * @param array $parameters
     * @param bool $encode
     */
    public function get_link($parameters = [], $filter = [], $encode_entities = false)
    {
        return $this->getUrlGenerator()->fromParameters($parameters, $filter, $encode_entities);
    }

    /**
     * Returns the value of the given URL parameter.
     *
     * @param string $name The parameter name.
     *
     * @return string The parameter value.
     */
    public function get_parameter($name)
    {
        if (array_key_exists($name, $this->parameters))
        {
            return $this->parameters[$name];
        }
    }

    /**
     * Returns the current URL parameters.
     *
     * @return array The parameters.
     */
    public function get_parameters()
    {
        return $this->parameters;
    }

    public function get_url($parameters = [], $filter = [])
    {
        return $this->getUrlGenerator()->fromParameters($parameters, $filter);
    }

    /**
     * @return User null
     */
    public function get_user()
    {
        return $this->getApplication()->get_user();
    }

    public function get_user_id()
    {
        return $this->get_user()->get_id();
    }

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * Sets the value of a URL parameter.
     *
     * @param string $name  The parameter name.
     * @param string $value The parameter value.
     */
    public function set_parameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * @param array $parameters
     */
    public function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
