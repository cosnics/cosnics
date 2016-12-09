<?php
namespace Chamilo\Core\Home\Renderer;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Core\Home\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Renderer
{
    const TYPE_BASIC = 'Basic';

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var array
     */
    private $parameters;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     *
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
     *
     * @return string
     */
    abstract public function render();

    public function getCurrentTabIdentifier()
    {
        return Request::get(self::PARAM_TAB_ID);
    }

    public function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $redirect = new Redirect($parameters, $filter, $encode_entities);
        return $redirect->getUrl();
    }

    /**
     * Gets a link to the personal calendar application
     * 
     * @param array $parameters
     * @param boolean $encode
     */
    public function get_link($parameters = array (), $filter = array(), $encode_entities = false)
    {
        $redirect = new Redirect($parameters, $filter, $encode_entities);
        return $redirect->getUrl();
    }

    public function get_home_tab_viewing_url($home_tab)
    {
        return $this->get_url(array(self::PARAM_TAB_ID => $home_tab->get_id()));
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

    /**
     * Returns the value of the given URL parameter.
     * 
     * @param string $name The parameter name.
     * @return string The parameter value.
     */
    public function get_parameter($name)
    {
        if (array_key_exists($name, $this->parameters))
            return $this->parameters[$name];
    }

    /**
     * Sets the value of a URL parameter.
     * 
     * @param string $name The parameter name.
     * @param string $value The parameter value.
     */
    public function set_parameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     *
     * @param array $parameters
     */
    public function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
