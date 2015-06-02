<?php
namespace Chamilo\Application\Weblcms\Course\Component;

use Chamilo\Application\Weblcms\Course\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Table\CourseTable\CourseTable;
use Chamilo\Application\Weblcms\Menu\CourseCategoryMenu;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * This class describes a browser for the courses
 *
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class BrowseComponent extends Manager implements TableSupport
{
    /**
     * The category id
     */
    const PARAM_CATEGORY_ID = 'category_id';

    /**
     * Keeps track of the action bar
     *
     * @var \libraries\format\ActionBarRenderer
     */
    private $action_bar;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if ($this->can_view_component())
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->get_html();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            throw new NotAllowedException();
        }
    }

    /**
     * Returns the condition for the table
     *
     * @param string $object_table_class_name
     *
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($object_table_class_name)
    {
        $conditions = array();

        $category_id = Request :: get(self :: PARAM_CATEGORY_ID);
        if ($category_id)
        {
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Course :: class_name(), Course :: PROPERTY_CATEGORY_ID),
                new StaticConditionVariable($category_id));
        }

        $search_condition = $this->action_bar->get_conditions(
            array(Course :: PROPERTY_TITLE, Course :: PROPERTY_VISUAL_CODE));

        if ($search_condition)
        {
            $conditions[] = $search_condition;
        }

        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the html for this component
     *
     * @return String
     */
    protected function get_html()
    {
        $html = array();

        $this->action_bar = $this->build_action_bar();

        $html[] = '<div style="clear: both;"></div>';
        $html[] = $this->action_bar->as_html() . '<br />';

        $temp_replacement = '__CATEGORY_ID__';

        $url_format = $this->get_url(array(self :: PARAM_CATEGORY_ID => $temp_replacement));
        $url_format = str_replace($temp_replacement, '%s', $url_format);

        $category_menu = new CourseCategoryMenu(Request :: get(self :: PARAM_CATEGORY_ID), $url_format);

        $html[] = '<div style="float: left; padding-right: 20px; width: 18%; overflow: auto; height: 100%;">';
        $html[] = $category_menu->render_as_tree();
        $html[] = '</div>';

        $course_table = $this->get_course_table();

        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $course_table->as_html();
        $html[] = '</div>';

        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode($html, "\n");
    }

    /**
     * Returns the course table for this component
     *
     * @return CourseTable
     */
    protected function get_course_table()
    {
        return new CourseTable($this);
    }

    /**
     * Checkes whether or not the current user can view this component
     *
     * @return boolean
     */
    protected function can_view_component()
    {
        return $this->get_user()->is_platform_admin();
    }

    /**
     * Creates and returns the action bar
     *
     * @return \libraries\format\ActionBarRenderer
     */
    protected function build_action_bar()
    {
        $action_bar = $this->build_basic_action_bar();

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Add'),
                $this->get_create_course_url(),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    /**
     * Builds the basic actionbar for this component
     *
     * @return \libraries\format\ActionBarRenderer
     */
    protected function build_basic_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the actionbar for this component
     *
     * @return ActionBarRenderer
     */
    public function get_action_bar()
    {
        return $this->action_bar;
    }

    public function get_parameters()
    {
        $parameters = parent :: get_parameters();
        $parameters[self :: PARAM_CATEGORY_ID] = Request :: get(self :: PARAM_CATEGORY_ID);

        if (isset($this->action_bar))
        {
            $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->get_action_bar()->get_query();
        }
        return $parameters;
    }

    /**
     * Sets the actionbar for this component
     *
     * @param $action_bar ActionBarRenderer
     */
    public function set_action_bar(ActionBarRenderer $action_bar)
    {
        $this->action_bar = $action_bar;
    }
}
