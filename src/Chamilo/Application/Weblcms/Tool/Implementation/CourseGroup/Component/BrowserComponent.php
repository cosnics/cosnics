<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\CourseGroupMenu;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\CourseGroup\CourseGroupTable;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\CourseGroup\CourseGroupTableDataProvider;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ConditionProperty;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: course_group_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
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
        $parameters = $this->get_parameters(true);

        $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = self :: ACTION_BROWSE;

        $course_group_table = new CourseGroupTable($this, new CourseGroupTableDataProvider($this));

        return $course_group_table->as_html();
    }

    /*
     * Returns the condition needed for the table
     *
     * @return Condition
     */
    public function get_table_condition($table_class_name)
    {
        $conditions = array();

        $properties = array();
        $properties[] = new PropertyConditionVariable(CourseGroup:: class_name(), CourseGroup :: PROPERTY_NAME);
        $properties[] = new PropertyConditionVariable(CourseGroup:: class_name(), CourseGroup :: PROPERTY_DESCRIPTION);
        $query_condition = $this->buttonToolbarRenderer->getConditions($properties);

        $root_course_group = $this->rootCourseGroup;

        $course_group_id = $this->get_group_id();

        if (!$course_group_id || ($root_course_group->get_id() == $course_group_id))
        {
            $root_course_group_id = $root_course_group->get_id();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup:: class_name(), CourseGroup :: PROPERTY_PARENT_ID),
                new StaticConditionVariable($root_course_group_id)
            );
        }
        else
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(CourseGroup:: class_name(), CourseGroup :: PROPERTY_PARENT_ID),
                new StaticConditionVariable($course_group_id)
            );
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
