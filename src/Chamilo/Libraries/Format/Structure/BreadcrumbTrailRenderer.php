<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BreadcrumbTrailRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function __construct(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities()
    {
        return $this->stringUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities($stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbTrail
     * @return string
     */
    public function render(BreadcrumbTrail $breadcrumbTrail)
    {
        if ($breadcrumbTrail->size() == 0)
        {
            return '';
        }

        $html = array();

        $html[] = '<div class="container-breadcrumb">';
        $html[] = '<div class="' . $breadcrumbTrail->getContainerMode() . '">';
        $html[] = $this->renderBreadcrumbs($breadcrumbTrail);
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbTrail
     * @return string
     */
    public function renderBreadcrumbs(BreadcrumbTrail $breadcrumbTrail)
    {
        $html = array();

        $html[] = '<ol class="breadcrumb">';

        $breadcrumbs = $breadcrumbTrail->get_breadcrumbs();

        if (is_array($breadcrumbs) && count($breadcrumbs) > 0)
        {
            foreach ($breadcrumbs as $breadcrumb)
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
                    $breadCrumbHtml[] = $this->getStringUtilities()->truncate($breadcrumb->get_name(), 50, true);
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
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbTrail
     * @return string
     */
    public function renderHelp(BreadcrumbTrail $breadcrumbTrail)
    {
        $html = array();
        $helpItem = $breadcrumbTrail->getHelpItem();

        if (is_array($helpItem) && count($helpItem) == 2)
        {
            $item = \Chamilo\Core\Help\Manager::get_tool_bar_help_item($helpItem);

            if ($item instanceof ToolbarItem)
            {
                $html[] = '<div id="help_item">';
                $toolbar = new Toolbar();
                $toolbar->set_items(array($item));
                $toolbar->set_type(Toolbar::TYPE_HORIZONTAL);
                $html[] = $toolbar->render();
                $html[] = '</div>';
            }
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbTrail
     * @return string
     */
    public function renderExtra(BreadcrumbTrail $breadcrumbTrail)
    {
        $html = array();

        $extraItems = $breadcrumbTrail->getExtraItems();

        $html[] = '<div id="extra_item">';
        $toolbar = new Toolbar();

        if (is_array($extraItems) && count($extraItems) > 0)
        {
            $toolbar->add_items($extraItems);
            $toolbar->set_type(Toolbar::TYPE_HORIZONTAL);
        }

        $html[] = $toolbar->render();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}

