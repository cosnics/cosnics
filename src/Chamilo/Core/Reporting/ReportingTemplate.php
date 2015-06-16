<?php
namespace Chamilo\Core\Reporting;

use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;

/**
 * $Id: reporting_template.class.php 215 2009-11-13 14:07:59Z vanpouckesven $ Extendable class for the reporting
 * templates This contains the general shared template properties such as Properties (name, description, etc) Layout
 * (header,menu, footer)
 *
 * @package reporting.lib
 * @author Michael Kyndt
 */
abstract class ReportingTemplate
{
    use \Chamilo\Libraries\Architecture\Traits\ClassContext;

    private $blocks = array();

    private $parent;

    public function __construct($parent)
    {
        $this->set_parent($parent);
    }

    /**
     *
     * @return number
     */
    public function count_blocks()
    {
        return count($this->get_blocks());
    }

    public function get_parent()
    {
        return $this->parent;
    }

    public function set_parent($parent)
    {
        $this->parent = $parent;
    }

    /**
     *
     * @return multitype:\reporting\ReportingBlock
     */
    public function get_blocks()
    {
        return $this->blocks;
    }

    /**
     *
     * @param multitype:\reporting\ReportingBlock $blocks
     */
    public function set_blocks($blocks)
    {
        $this->blocks = $blocks;
    }

    /**
     *
     * @param int $reporting_block_id
     * @return \core\reporting\ReportingBlock
     */
    public function get_block($block_id)
    {
        return $this->blocks[$block_id];
    }

    public function add_reporting_block($block)
    {
        $block->set_id(count($this->blocks));
        $this->blocks[] = $block;
    }

    public function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }

    public function get_parameter($key)
    {
        return $this->get_parent()->get_parameter($key);
    }

    public function set_parameters($parameters)
    {
        $this->get_parent()->set_parameters($parameters);
    }

    public function set_parameter($key, $value)
    {
        $this->get_parent()->set_parameter($key, $value);
    }

    /**
     * This method allows replacing the last crumb of the breadcrumbtrail with one or more own crumbs
     *
     * @param array custom_breadcrumbs
     */
    public function set_custom_breadcrumb_trail($custom_breadcrumbs)
    {
        $breadcrumb_trail = BreadcrumbTrail :: get_instance();
        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();
        $breadcrumbs[$breadcrumb_trail->size() - 1] = $custom_breadcrumbs[0];

        for ($i = 0; $i < count($custom_breadcrumbs) - 1; $i ++)
        {
            $breadcrumbs[$breadcrumb_trail->size() + $i] = $custom_breadcrumbs[$i + 1];
        }
        $breadcrumb_trail->set_breadcrumbtrail($breadcrumbs);
    }

    public function get_url($parameters = array(), $filter = array(), $encode_entities = false)
    {
        return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
    }

    /**
     * @brief Return template style containing properties such as title font size or paper orientation.
     * Default implementation retrieves values from the cental configuration. See
     * src/Chamilo/Core/Reporting/Resources/Settings/settings.xml.
     * Templates can override this function and return a ReportingTemplateStyle object with custom properties.
     */
    public function getStyle()
    {
        return new ReportingTemplateStyle();
    }

    /**
     *
     * @return string
     */
    public static function package()
    {
        return static :: context();
    }
}
