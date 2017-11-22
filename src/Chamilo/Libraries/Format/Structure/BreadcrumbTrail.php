<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Configuration\Service\FileConfigurationLocator;

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
     * @var \Chamilo\Libraries\Format\Structure\BreadcrumbTrail
     */
    private static $instance;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\Breadcrumb[]
     */
    private $breadcrumbtrail;

    /**
     *
     * @var string[]
     */
    private $help_item;

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    private $extra_items;

    /**
     *
     * @var string
     */
    private $containerMode;

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\BreadcrumbTrail
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
     * @param boolean $includeMainIndex
     * @param string $containerMode
     */
    public function __construct($includeMainIndex = true, $containerMode = 'container-fluid')
    {
        $this->breadcrumbtrail = array();
        $this->extra_items = array();
        $this->containerMode = $containerMode;

        if ($includeMainIndex)
        {
            $pathBuilder = new PathBuilder(new ClassnameUtilities(new StringUtilities()));

            $fileConfigurationLocator = new FileConfigurationLocator($pathBuilder);

            // TODO: Can this be fixed more elegantly?
            if ($fileConfigurationLocator->isAvailable())
            {
                $siteName = $this->get_setting('site_name', 'Chamilo\Core\Admin');
            }
            else
            {
                $siteName = 'Chamilo';
            }

            $this->add(new Breadcrumb($pathBuilder->getBasePath(true), $siteName, null, 'home'));
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
     * @param \Chamilo\Libraries\Format\Structure\Breadcrumb $breadcrumb
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
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem $extraItem
     */
    public function add_extra($extraItem)
    {
        $this->extra_items[] = $extraItem;
    }

    /**
     *
     * @return string[]
     */
    public function get_help_item()
    {
        return $this->help_item;
    }

    /**
     *
     * @param string $helpItem
     */
    public function set_help_item($helpItem)
    {
        $this->help_item = $helpItem;
    }

    public function remove($breadcrumbIndex)
    {
        if ($breadcrumbIndex < 0)
        {
            $breadcrumbIndex = count($this->breadcrumbtrail) + $breadcrumbIndex;
        }

        unset($this->breadcrumbtrail[$breadcrumbIndex]);
        $this->breadcrumbtrail = array_values($this->breadcrumbtrail);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\Breadcrumb
     */
    public function get_first()
    {
        return $this->breadcrumbtrail[0];
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\Breadcrumb
     */
    public function get_last()
    {
        $breadcrumbtrail = $this->breadcrumbtrail;
        $last_key = count($breadcrumbtrail) - 1;
        return $breadcrumbtrail[$last_key];
    }

    /**
     *
     * @param boolean $keepMainIndex
     */
    public function truncate($keepMainIndex = false)
    {
        $this->breadcrumbtrail = array();
        if ($keepMainIndex)
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
     * @return integer
     */
    public function size()
    {
        return count($this->breadcrumbtrail);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\Breadcrumb[]
     */
    public function get_breadcrumbtrail()
    {
        return $this->breadcrumbtrail;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\Breadcrumb[] $breadcrumbtrail
     * @deprecated Deprecated method
     */
    public function set_breadcrumbtrail($breadcrumbtrail)
    {
        $this->set($breadcrumbtrail);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\Breadcrumb[] $breadcrumbs
     */
    public function set($breadcrumbs)
    {
        $this->breadcrumbtrail = $breadcrumbs;
    }

    /**
     *
     * @param string $variable
     * @param string $application
     * @return string
     */
    public function get_setting($variable, $application)
    {
        return Configuration::getInstance()->get_setting(array($application, $variable));
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\Breadcrumb[]
     */
    public function get_breadcrumbs()
    {
        return $this->breadcrumbtrail;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $trail
     */
    public function merge($trail)
    {
        $this->breadcrumbtrail = array_merge($this->breadcrumbtrail, $trail->get_breadcrumbtrail());
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\Breadcrumb[]
     */
    public function getBreadcrumbs()
    {
        return $this->breadcrumbtrail;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\Breadcrumb[] $breadcrumbs
     */
    public function setBreadcrumbs($breadcrumbs)
    {
        $this->breadcrumbtrail = $breadcrumbs;
    }

    /**
     *
     * @return string[]
     */
    public function getHelpItem()
    {
        return $this->help_item;
    }

    /**
     *
     * @param string[] $helpItem
     */
    public function setHelpItem($helpItem)
    {
        $this->help_item = $helpItem;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ToolbarItem[]
     */
    public function getExtraItems()
    {
        return $this->extra_items;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\ToolbarItem[] $extraItems
     */
    public function setExtraItems($extraItems)
    {
        $this->extra_items = $extraItems;
    }
}
