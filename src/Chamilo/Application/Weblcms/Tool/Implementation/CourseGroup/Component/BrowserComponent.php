<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\CourseGroup\CourseGroupTable;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component
 */
class BrowserComponent extends TabComponent implements TableSupport
{

    /**
     * Renders the content of the current tab
     */
    public function renderTabContent()
    {
        $html = array();

        $html[] = $this->getTableHtml();

        return implode(PHP_EOL, $html);
    }

    /**
     * Renders the table as html
     *
     * @return string
     */
    protected function getTableHtml()
    {
        $parameters = $this->get_parameters();
        $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = self::ACTION_BROWSE;

        $course_group_table = new CourseGroupTable($this);

        return $course_group_table->as_html();
    }

    /*
     * Returns the condition needed for the table
     * @return Condition
     */
    public function get_table_condition($table_class_name)
    {
        $conditions = array();

        $properties = array();
        $properties[] = new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME);
        $properties[] = new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_DESCRIPTION);
        $query_condition = $this->buttonToolbarRenderer->getConditions($properties);

        $root_course_group = $this->rootCourseGroup;

        $course_group_id = $this->get_group_id();

        if (! $course_group_id || ($root_course_group->get_id() == $course_group_id))
        {
            $root_course_group_id = $root_course_group->get_id();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_PARENT_ID),
                new StaticConditionVariable($root_course_group_id));
        }
        else
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_PARENT_ID),
                new StaticConditionVariable($course_group_id));
        }

        if ($query_condition)
        {
            $conditions[] = $query_condition;
        }

        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }

        return null;
    }
}
