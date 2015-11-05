<?php
namespace Chamilo\Application\Weblcms\CourseType\Component;

use Chamilo\Application\Weblcms\CourseType\Manager;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Table\CourseType\CourseTypeTable;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class describes a browser for the course types
 *
 * @package \application\weblcms\course_type
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class BrowseComponent extends Manager implements TableSupport
{

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
        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->get_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Breadcrumbs are built semi automatically with the given application, subapplication, component... Use this
     * function to add other breadcrumbs between the application / subapplication and the current component
     *
     * @param $breadcrumbtrail \libraries\format\BreadcrumbTrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_type_browser');
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
    private function get_html()
    {
        $html = array();

        $this->action_bar = $this->get_action_bar();

        $html[] = '<div style="clear: both;"></div>';
        $html[] = $this->action_bar->as_html() . '<br />';

        $course_type_table = new CourseTypeTable($this);

        $html[] = $course_type_table->as_html();
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode($html, "\n");
    }

    /**
     * Creates and returns the action bar
     *
     * @return \libraries\format\ActionBarRenderer
     */
    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Add'),
                $this->get_create_course_type_url(),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                $this->get_url(),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    /**
     * Returns the condition for the table
     *
     * @return \libraries\storage\Condition
     */
    public function get_table_condition($object_table_class_name)
    {
        return $this->action_bar->get_conditions(
            array(CourseType :: PROPERTY_TITLE, CourseType :: PROPERTY_DESCRIPTION));
    }
}
