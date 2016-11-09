<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BreadcrumbTrail
{

    /**
     * Singleton
     *
     * @var BreadcrumbTrail
     */
    private static $instance;

    /**
     *
     * @var array
     */
    private $breadcrumbtrail;

    /**
     *
     * @var array
     */
    private $help_item;

    /**
     *
     * @var array
     */
    private $extra_items;

    /**
     *
     * @var string
     */
    private $containerMode;

    /**
     *
     * @return BreadcrumbTrail
     */
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new BreadcrumbTrail(true, 'container-fluid');
        }

        return self::$instance;
    }

    /**
     *
     * @param boolean $include_main_index
     */
    public function __construct($include_main_index = true, $containerMode = 'container-fluid')
    {
        $this->breadcrumbtrail = array();
        $this->extra_items = array();
        $this->containerMode = $containerMode;

        if ($include_main_index)
        {
            $this->add(
                new Breadcrumb(
                    Path::getInstance()->getBasePath(true) . 'index.php',
                    $this->get_setting('site_name', 'Chamilo\Core\Admin'),
                    null,
                    'home'));
        }
    }

    /**
     *
     * @return string
     */
    public function getContainerMode()
    {
        return $this->containerMode;
    }

    /**
     *
     * @param string $containerMode
     */
    public function setContainerMode($containerMode)
    {
        $this->containerMode = $containerMode;
    }

    /**
     *
     * @param Breadcrumb $breadcrumb
     */
    public function add($breadcrumb)
    {
        $this->breadcrumbtrail[] = $breadcrumb;
    }

    /**
     *
     * @param string $context
     * @param string $identifier
     */
    public function add_help($context, $identifier = null)
    {
        $this->set_help_item(array($context, $identifier));
    }

    /**
     *
     * @param ToolbarItem $extra_item
     */
    public function add_extra($extra_item)
    {
        $this->extra_items[] = $extra_item;
    }

    /**
     *
     * @return array
     */
    public function get_help_item()
    {
        return $this->help_item;
    }

    /**
     *
     * @param array $help_item
     */
    public function set_help_item($help_item)
    {
        $this->help_item = $help_item;
    }

    public function remove($breadcrumb_index)
    {
        if ($breadcrumb_index < 0)
        {
            $breadcrumb_index = count($this->breadcrumbtrail) + $breadcrumb_index;
        }

        unset($this->breadcrumbtrail[$breadcrumb_index]);
        $this->breadcrumbtrail = array_values($this->breadcrumbtrail);
    }

    public function get_first()
    {
        return $this->breadcrumbtrail[0];
    }

    public function get_last()
    {
        $breadcrumbtrail = $this->breadcrumbtrail;
        $last_key = count($breadcrumbtrail) - 1;
        return $breadcrumbtrail[$last_key];
    }

    public function truncate($keep_main_index = false)
    {
        $this->breadcrumbtrail = array();
        if ($keep_main_index)
        {
            $this->add(
                new Breadcrumb(
                    Path::getInstance()->getBasePath(true) . 'index.php',
                    $this->get_setting('site_name', 'Chamilo\Core\Admin')));
        }
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        $html[] = '<div class="container-breadcrumb">';
        $html[] = '<div class="' . $this->getContainerMode() . '">';
        $html[] = $this->render_breadcrumbs();
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function render_breadcrumbs()
    {
        $html = array();
        $html[] = '<ol class="breadcrumb">';

        $breadcrumbtrail = $this->breadcrumbtrail;
        if (is_array($breadcrumbtrail) && count($breadcrumbtrail) > 0)
        {
            foreach ($breadcrumbtrail as $breadcrumb)
            {
                $breadCrumbHtml = array();

                $breadCrumbHtml[] = '<li>';
                $breadCrumbHtml[] = '<a href="' . htmlentities($breadcrumb->get_url()) . '" target="_self">';

                if ($breadcrumb->getImage())
                {
                    $breadCrumbHtml[] = '<img src="' . $breadcrumb->getImage() . '" title="' .
                         htmlentities($breadcrumb->get_name()) . '">';
                }
                elseif ($breadcrumb->getGlyph())
                {
                    $breadCrumbHtml[] = '<span class="glyphicon glyphicon-' . $breadcrumb->getGlyph() . '"></span>';
                }
                else
                {
                    $breadCrumbHtml[] = StringUtilities::getInstance()->truncate($breadcrumb->get_name(), 50, true);
                }

                $breadCrumbHtml[] = '</a>';
                $breadCrumbHtml[] = '</li>';

                $html[] = implode('', $breadCrumbHtml);
            }
        }

        $html[] = '</ol>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function render_help()
    {
        $html = array();
        $help_item = $this->help_item;

        if (is_array($help_item) && count($help_item) == 2)
        {
            $item = \Chamilo\Core\Help\Manager::get_tool_bar_help_item($help_item);

            if ($item instanceof ToolbarItem)
            {
                $html[] = '<div id="help_item">';
                $toolbar = new Toolbar();
                $toolbar->set_items(array($item));
                $toolbar->set_type(Toolbar::TYPE_HORIZONTAL);
                $html[] = $toolbar->as_html();
                $html[] = '</div>';
            }
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function render_extra()
    {
        $html = array();
        $extra_items = $this->extra_items;

        $html[] = '<div id="extra_item">';
        $toolbar = new Toolbar();

        if (is_array($extra_items) && count($extra_items) > 0)
        {
            $toolbar->add_items($extra_items);
            $toolbar->set_type(Toolbar::TYPE_HORIZONTAL);
        }

        $html[] = $toolbar->as_html();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return number
     */
    public function size()
    {
        return count($this->breadcrumbtrail);
    }

    /**
     *
     * @return array
     */
    public function get_breadcrumbtrail()
    {
        return $this->breadcrumbtrail;
    }

    /**
     *
     * @param unknown $breadcrumbtrail
     * @deprecated Deprecated method
     */
    public function set_breadcrumbtrail($breadcrumbtrail)
    {
        $this->set($breadcrumbtrail);
    }

    public function set($breadcrumbs)
    {
        $this->breadcrumbtrail = $breadcrumbs;
    }

    /**
     *
     * @param string $variable
     * @param string $application
     * @return mixed
     */
    public function get_setting($variable, $application)
    {
        return Configuration::getInstance()->get_setting(array($application, $variable));
    }

    /**
     *
     * @return array
     */
    public function get_breadcrumbs()
    {
        return $this->breadcrumbtrail;
    }

    public function merge($trail)
    {
        $this->breadcrumbtrail = array_merge($this->breadcrumbtrail, $trail->get_breadcrumbtrail());
    }
}
