<?php
namespace Chamilo\Core\Reporting;

use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * templates This contains the general shared template properties such as Properties (name, description, etc) Layout
 * (header,menu, footer)
 *
 * @package reporting.lib
 * @author  Michael Kyndt
 */
abstract class ReportingTemplate
{
    use DependencyInjectionContainerTrait;

    private $blocks = [];

    private $parent;

    public function __construct($parent)
    {
        $this->set_parent($parent);
    }

    public function addCurrentBlockBreadcrumb()
    {
        if ($this->getRequest()->query->get(\Chamilo\Core\Reporting\Viewer\Manager::PARAM_SHOW_ALL))
        {
            return;
        }

        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                $this->get_url(), $this->getCurrentBlock()->get_title()
            )
        );
    }

    public function add_reporting_block($block)
    {
        $block->set_id(count($this->blocks));
        $this->blocks[] = $block;
    }

    /**
     * @return number
     */
    public function count_blocks()
    {
        return count($this->get_blocks());
    }

    /**
     * @return \Chamilo\Core\Reporting\ReportingBlock
     */
    public function getCurrentBlock()
    {
        $blockId = $this->getRequest()->query->get(\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID);
        $block = null;

        if ($blockId >= 0)
        {
            $block = $this->get_block($blockId);
        }

        if (!$block instanceof ReportingBlock)
        {
            $block = $this->get_block(0);
        }

        return $block;
    }

    /**
     * @brief Return template style containing properties such as title font size or paper orientation.
     * Default implementation retrieves values from the cental configuration. See
     * src/Chamilo/Core/Reporting/Resources/Settings/settings.xml.
     * Templates can override this function and return a ReportingTemplateStyle object with custom properties.
     */
    public function getStyle()
    {
        return new ReportingTemplateStyle($this->getConfigurationConsulter());
    }

    /**
     * @param int $block_id
     *
     * @return \Chamilo\Core\Reporting\ReportingBlock
     */
    public function get_block($block_id)
    {
        return $this->blocks[$block_id];
    }

    /**
     * @return \Chamilo\Core\Reporting\ReportingBlock[]
     */
    public function get_blocks()
    {
        return $this->blocks;
    }

    public function get_parameter($key)
    {
        return $this->get_parent()->get_parameter($key);
    }

    public function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }

    public function get_parent()
    {
        return $this->parent;
    }

    public function get_url($parameters = [], $filter = [], $encode_entities = false)
    {
        return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
    }

    /**
     * @param \Chamilo\Core\Reporting\ReportingBlock[] $blocks
     */
    public function set_blocks($blocks)
    {
        $this->blocks = $blocks;
    }

    /**
     * This method allows replacing the last crumb of the breadcrumbtrail with one or more own crumbs
     *
     * @param array custom_breadcrumbs
     */
    public function set_custom_breadcrumb_trail($custom_breadcrumbs)
    {
        $breadcrumb_trail = BreadcrumbTrail::getInstance();
        $breadcrumbs = $breadcrumb_trail->getBreadcrumbs();
        $breadcrumbs[$breadcrumb_trail->size() - 1] = $custom_breadcrumbs[0];

        for ($i = 0; $i < count($custom_breadcrumbs) - 1; $i ++)
        {
            $breadcrumbs[$breadcrumb_trail->size() + $i] = $custom_breadcrumbs[$i + 1];
        }
        $breadcrumb_trail->set_breadcrumbtrail($breadcrumbs);
    }

    public function set_parameter($key, $value)
    {
        $this->get_parent()->set_parameter($key, $value);
    }

    public function set_parameters($parameters)
    {
        $this->get_parent()->set_parameters($parameters);
    }

    public function set_parent($parent)
    {
        $this->parent = $parent;
    }
}
