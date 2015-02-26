<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Renderer\CourseList\Type\CourseTypeCourseListRenderer;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: home.class.php 218 2009-11-13 14:21:26Z kariboe $
 *
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component which provides the user with a list of all courses he or she has subscribed to.
 */
class HomeComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $renderer = new CourseTypeCourseListRenderer($this);

        $renderer->show_new_publication_icons();

        $html = array();

        $html[] = $this->render_header();
        $html[] = '<div class="clear"></div>';
        $html[] = $this->display_menu();
        $html[] = '<div id="tool_browser_right">';

        $html[] = $renderer->as_html();
        $html[] = '<script type="text/javascript" src="' .
             htmlspecialchars(Path :: getInstance()->namespaceToFullPath('Chamilo\Configuration', true)) .
             'Resources/Javascript/HomeAjax.js' . '"></script>';

        $toolbar_state = Session :: retrieve('toolbar_state');

        if ($toolbar_state == 'hide')
        {
            $html[] = '<script type="text/javascript">var hide = "true";</script>';
        }
        else
        {
            $html[] = '<script type="text/javascript">var hide = "false";</script>';
        }

        $html[] = '</div>';
        $html[] = $this->render_footer();

        return implode("\n", $html);
    }

    public function display_menu()
    {
        $html = array();

        $html[] = '<div id="tool_bar" class="tool_bar tool_bar_right">';

        $img_path = htmlspecialchars(Theme :: getInstance()->getCommonImagePath());
        $html[] = '<div id="tool_bar_hide_container" class="hide">';
        $html[] = '<a id="tool_bar_hide" href="#"><img src="' . $img_path . 'action_action_bar_right_hide.png" /></a>';
        $html[] = '<a id="tool_bar_show" href="#"><img src="' . $img_path . 'action_action_bar_right_show.png" /></a>';
        $html[] = '</div>';

        $html[] = '<div class="tool_menu">';
        $html[] = '<ul>';

        if ($this->get_user()->is_platform_admin())
        {
            $html[] = '<li class="tool_list_menu title" style="font-weight: bold">' . htmlspecialchars(
                Translation :: get('CourseManagement')) . '</li><br />';
            $html[] = $this->display_platform_admin_course_list_links();
            $html[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
        }
        else
        {

            $display_add_course_link = $this->get_user()->is_teacher() && ($_SESSION["studentview"] != "studentenview");
            if ($display_add_course_link)
            {
                $display = $this->display_create_course_link();

                if ($display)
                {
                    $html[] = '<li class="tool_list_menu" style="font-weight: bold">' . htmlspecialchars(
                        Translation :: get('MenuUser')) . '</li><br />';
                    $html[] = $display;
                }
            }
        }

        $html[] = '<li class="tool_list_menu title" style="font-weight: bold">' . htmlspecialchars(
            Translation :: get('UserCourseManagement')) . '</li><br />';
        $html[] = $this->display_edit_course_list_links();
        $html[] = '</ul>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '<script type="text/javascript" src="' .
             htmlspecialchars(Path :: getInstance()->namespaceToFullPath('Chamilo\Configuration', true)) .
             'Resources/Javascript/ToolBar.js' . '"></script>';
        $html[] = '<div class="clear"></div>';
        return implode($html, "\n");
    }

    public function display_create_course_link()
    {
        $html = array();

        $img_path = htmlspecialchars(Theme :: getInstance()->getCommonImagePath());

        $course_management_rights = CourseManagementRights :: get_instance();

        $count_direct = $count_request = 0;

        $course_types = \Chamilo\Application\Weblcms\CourseType\Storage\DataManager :: retrieve_active_course_types();
        while ($course_type = $course_types->next_result())
        {
            if ($course_management_rights->is_allowed(
                CourseManagementRights :: CREATE_COURSE_RIGHT,
                $course_type->get_id(),
                CourseManagementRights :: TYPE_COURSE_TYPE))
            {
                $count_direct ++;
            }
            elseif ($course_management_rights->is_allowed(
                CourseManagementRights :: REQUEST_COURSE_RIGHT,
                $course_type->get_id(),
                CourseManagementRights :: TYPE_COURSE_TYPE))
            {
                $count_request ++;
            }
        }

        if (PlatformSetting :: get('allow_course_creation_without_coursetype', 'Chamilo\Application\Weblcms'))
        {
            $count_direct ++;
        }

        if ($count_direct)
        {
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
                 'action_create.png)"><a style="top: -3px; position: relative;" href="' .
                 htmlspecialchars(
                    $this->get_url(
                        array(
                            Application :: PARAM_ACTION => self :: ACTION_COURSE_MANAGER,
                            \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager :: ACTION_QUICK_CREATE))) .
                 '">' . htmlspecialchars(Translation :: get('CourseCreate')) . '</a></li>';
        }

        if ($count_request)
        {
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
                 'action_create.png)"><a style="top: -3px; position: relative;" href="' .
                 htmlspecialchars(
                    $this->get_url(
                        array(
                            Application :: PARAM_ACTION => self :: ACTION_REQUEST,
                            \Chamilo\Application\Weblcms\Request\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Request\Manager :: ACTION_CREATE))) .
                 '">' . Utilities :: htmlentities(Translation :: get('CourseRequest')) . '</a></li>';
        }

        if (\Chamilo\Application\Weblcms\Request\Rights\Rights :: get_instance()->request_is_allowed())
        {
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
                 'action_browser.png)"><a style="top: -3px; position: relative;" href="' .
                 htmlspecialchars($this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_REQUEST))) . '">' . htmlspecialchars(
                    Translation :: get('RequestList')) . '</a></li>';
        }

        return implode("\n", $html);
    }

    public function display_edit_course_list_links()
    {
        $html = array();
        $img_path = htmlspecialchars(Theme :: getInstance()->getCommonImagePath());
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
             'action_reset.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_MANAGER_SORT))) . '">' .
             htmlspecialchars(Translation :: get('SortMyCourses')) . '</a></li>';

        if (PlatformSetting :: get('show_subscribe_button_on_course_home', __NAMESPACE__))
        {
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path . 'action_subscribe.png)">' .
                 '<a style="top: -3px; position: relative;" href="' .
                 htmlspecialchars(
                    $this->get_url(
                        array(
                            Application :: PARAM_ACTION => self :: ACTION_COURSE_MANAGER,
                            \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager :: ACTION_BROWSE_UNSUBSCRIBED_COURSES))) .
                 '">' . htmlspecialchars(Translation :: get('CourseSubscribe')) . '</a></li>';
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
                 'action_unsubscribe.png)"><a style="top: -3px; position: relative;" href="' .
                 htmlspecialchars(
                    $this->get_url(
                        array(
                            Application :: PARAM_ACTION => self :: ACTION_COURSE_MANAGER,
                            \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager :: ACTION_BROWSE_SUBSCRIBED_COURSES))) .
                 '">' . htmlspecialchars(Translation :: get('CourseUnsubscribe')) . '</a></li>';
        }

        return implode($html, "\n");
    }

    public function display_platform_admin_course_list_links()
    {
        $html = array();
        $img_path = htmlspecialchars(Theme :: getInstance()->getCommonImagePath());
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
             'action_create.png)"><a style="top: -3px; position: relative;" href="' .
             htmlspecialchars(
                $this->get_url(
                    array(
                        Application :: PARAM_ACTION => self :: ACTION_COURSE_MANAGER,
                        \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager :: ACTION_QUICK_CREATE))) .
             '">' . htmlspecialchars(Translation :: get('CourseCreate')) . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
             'action_browser.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_COURSE_MANAGER))) . '">' .
             htmlspecialchars(Translation :: get('CourseList')) . '</a></li>';

        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
             'action_browser.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_REQUEST))) . '">' .
             htmlspecialchars(Translation :: get('RequestList')) . '</a></li>';

        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
             'action_browser.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_ADMIN_REQUEST_BROWSER))) . '">' .
             htmlspecialchars(Translation :: get('UserRequestList')) . '</a></li>';

        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
             'action_move.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_COURSE_CATEGORY_MANAGER))) . '">' .
             htmlspecialchars(Translation :: get('CourseCategoryManagement')) . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
             'action_add.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_IMPORT_COURSES))) . '">' .
             htmlspecialchars(Translation :: get('ImportCourseCSV')) . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
             'action_add.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_IMPORT_COURSE_USERS))) . '">' .
             htmlspecialchars(Translation :: get('ImportUsersForCourseCSV')) . '</a></li>';

        $html[] = '<li class="tool_list_menu" style="background-image: url(' . $img_path .
             'action_add.png)"><a style="top: -3px; position: relative;" href="' . htmlspecialchars(
                $this->get_url(array(Application :: PARAM_ACTION => self :: ACTION_REPORTING))) . '">' .
             htmlspecialchars(Translation :: get('Reporting')) . '</a></li>';

        return implode($html, "\n");
    }

    public function get_course_user_category_actions(CourseUserCategory $course_user_category, CourseType $course_type,
        $offset, $count)
    {
        $img_path = htmlspecialchars(Theme :: getInstance()->getCommonImagePath());
        return '<a href="#" class="closeEl"><img class="visible" src="' . $img_path .
             'action_visible.png"/><img class="invisible" style="display: none;" src="' . $img_path .
             'action_invisible.png" /></a>';
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_course_home');
    }

    public function get_additional_parameters()
    {
        return array();
    }

    public function show_empty_courses()
    {
        return false;
    }
}
