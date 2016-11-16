<?php
namespace Chamilo\Core\Repository\ContentObject\Survey;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @package repository\content_object\survey
 */
class ComplexContentObjectPathNode extends \Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode
{
    const PROPERTY_NODE_IN_MENU = 'node_in_menu';
    const PROPERTY_PREVIOUS_PAGE_STEP = 'previous_page_step';
    const PROPERTY_NEXT_PAGE_STEP = 'next_page_step';
    const PROPERTY_PAGE_STEP_COUNT = 'page_step_count';
    const PROPERTY_LAST_PAGE_STEP = 'last_page_step';

    function in_menu()
    {
        return $this->get_property(self::PROPERTY_NODE_IN_MENU);
    }

    function get_previous_page_step()
    {
        return $this->get_property(self::PROPERTY_PREVIOUS_PAGE_STEP);
    }

    function get_next_page_step()
    {
        return $this->get_property(self::PROPERTY_NEXT_PAGE_STEP);
    }

    function set_next_page_step($step)
    {
        return $this->set_property(self::PROPERTY_NEXT_PAGE_STEP, $step);
    }

    function get_page_step_count()
    {
        return $this->get_property(self::PROPERTY_PAGE_STEP_COUNT);
    }

    function set_page_step_count($count)
    {
        return $this->set_property(self::PROPERTY_PAGE_STEP_COUNT, $count);
    }

    function get_last_page_step()
    {
        return $this->get_property(self::PROPERTY_LAST_PAGE_STEP);
    }

    function set_last_page_step($step)
    {
        return $this->set_property(self::PROPERTY_LAST_PAGE_STEP, $step);
    }
}
