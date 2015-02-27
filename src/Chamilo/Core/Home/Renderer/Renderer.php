<?php
namespace Chamilo\Core\Home\Renderer;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Exception;

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
    const TYPE_WIDGET = 'Widget';
    const TYPE_MY_CHAMILO_OS = 'MyChamiloOs';
    const PARAM_TAB_ID = 'tab';
    const PARAM_VIEW_TYPE = 'view_type';
    const PARAM_WIDGET_ID = 'widget_id';
    const PARAM_SECURITY_TOKEN = 'sid';

    /**
     *
     * @var User null
     */
    private $user;

    /**
     *
     * @var array
     */
    private $parameters;

    /**
     *
     * @param User|null $user
     */
    public function __construct($user = null)
    {
        $this->user = $user;
    }

    /**
     *
     * @return User null
     */
    public function get_user()
    {
        return $this->user;
    }

    public function get_user_id()
    {
        return $this->get_user()->get_id();
    }

    /**
     *
     * @param string $type
     * @param User|null $user
     * @return MenuRenderer
     */
    public static function factory($type, $user)
    {
        $class = __NAMESPACE__ . '\Type\\' . $type;

        if (! class_exists($class))
        {
            throw new Exception(Translation :: get('HomeRendererTypeDoesNotExist', array('type' => $type)));
        }

        return new $class($user);
    }

    /**
     *
     * @param string $type
     * @param User|null $user
     * @return string
     */
    public static function as_html($type, $user)
    {
        return self :: factory($type, $user)->render();
    }

    /**
     *
     * @return string
     */
    abstract public function render();

    public function render_header()
    {
        return Display :: header();
    }

    public function render_footer()
    {
        $html = array();

        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = Display :: footer();

        return implode(PHP_EOL, $html);
    }

    public function get_current_tab()
    {
        return Request :: get(self :: PARAM_TAB_ID);
    }

    public function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        // $parameters = (count($parameters) ? array_merge($this->get_parameters(), $parameters) :
        // $this->get_parameters());
        return Redirect :: get_url($parameters, $filter, $encode_entities);
    }

    /**
     * Gets a link to the personal calendar application
     *
     * @param array $parameters
     * @param boolean $encode
     */
    public function get_link($parameters = array (), $filter = array(), $encode_entities = false)
    {
        // Use this untill PHP 5.3 is available
        // Then use get_class($this) :: APPLICATION_NAME
        // and remove the get_application_name function();
        return Redirect :: get_link($parameters, $filter, $encode_entities);
    }

    public function get_home_tab_viewing_url($home_tab)
    {
        return $this->get_url(array(self :: PARAM_TAB_ID => $home_tab->get_id()));
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
