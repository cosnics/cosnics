<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\CourseType\Storage\DataManager as CourseTypeDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\String\SimpleTemplate;

/**
 * Block that displays main course's actions available in the main course menu.
 * That is create course,
 * register/unregister to course, etc. Do not display less common actions such as manage categories.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 */
class CourseMenu extends Block
{

    public function is_teacher()
    {
        return $this->get_user()->is_teacher();
    }

    public function display_content()
    {
        $html = array();
        $html[] = '<div class="tool_menu">';
        $html[] = '<ul>';
        $html[] = '{$ADMIN_MENU}';
        $html[] = '{$USER_MENU}';
        $html[] = '</ul>';
        $html[] = '</div>';

        $target = $this->get_link_target();

        $template = '<li class="tool_list_menu" style="background-image: url({$IMG})">
        <a style="top: -3px; position: relative;" href="{$HREF}" target="' . $target . '">{$TEXT}</a></li>';

        $ADMIN_MENU = $this->display_admin_menu($template);
        $USER_MENU = SimpleTemplate :: all($template, $this->get_edit_course_menu());

        $this->display_admin_menu($template);
        SimpleTemplate :: all($template, $this->get_edit_course_menu());

        return SimpleTemplate :: ex($html, array('ADMIN_MENU' => $ADMIN_MENU, 'USER_MENU' => $USER_MENU));
    }

    public function display_admin_menu($template)
    {
        $result = array();
        if ($this->get_user()->is_platform_admin())
        {
            $menu = $this->get_platform_admin_menu();
            $result[] = SimpleTemplate :: all($template, $menu);
            $result[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
        }
        else
        {
            if ($menu = $this->get_create_course_menu())
            {
                $result[] = SimpleTemplate :: all($template, $menu);
            }
        }
        return implode(PHP_EOL, $result);
    }

    public function get_create_course_menu()
    {
        if (! $this->is_teacher())
        {
            return '';
        }

        $result = array();

        $course_management_rights = \Chamilo\Application\Weblcms\Rights\CourseManagementRights :: get_instance();

        $count_direct = $count_request = 0;

        $course_types = CourseTypeDataManager :: retrieve_active_course_types();
        while ($course_type = $course_types->next_result())
        {
            if ($course_management_rights->is_allowed(
                \Chamilo\Application\Weblcms\Rights\CourseManagementRights :: CREATE_COURSE_RIGHT,
                $course_type->get_id(),
                \Chamilo\Application\Weblcms\Rights\CourseManagementRights :: TYPE_COURSE_TYPE))
            {
                $count_direct ++;
            }
            elseif ($course_management_rights->is_allowed(
                \Chamilo\Application\Weblcms\Rights\CourseManagementRights :: REQUEST_COURSE_RIGHT,
                $course_type->get_id(),
                \Chamilo\Application\Weblcms\Rights\CourseManagementRights :: TYPE_COURSE_TYPE))
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
            $HREF = $this->get_course_action_url(
                \Chamilo\Application\Weblcms\Manager :: ACTION_COURSE_MANAGER,
                array(
                    \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager :: ACTION_QUICK_CREATE));
            $TEXT = htmlspecialchars(Translation :: get('CourseCreate'));
            $IMG = Theme :: getInstance()->getCommonImagePath() . 'action_create.png';
            $result[] = compact('HREF', 'TEXT', 'IMG');
        }

        if ($count_request)
        {
            $HREF = $this->get_url(
                array(
                    Application :: PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager :: context(),
                    Application :: PARAM_ACTION => self :: ACTION_REQUEST,
                    \Chamilo\Application\Weblcms\Request\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Request\Manager :: ACTION_CREATE));

            $TEXT = htmlspecialchars(Translation :: get('CourseRequest'));
            $IMG = Theme :: getInstance()->getCommonImagePath() . 'action_create.png';
            $result[] = compact('HREF', 'TEXT', 'IMG');
        }

        return $result;
    }

    public function get_edit_course_menu()
    {
        $result = array();

        $HREF = $this->get_course_action_url(
            \Chamilo\Application\Weblcms\Manager :: ACTION_COURSE_MANAGER,
            array(
                \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager :: ACTION_BROWSE_UNSUBSCRIBED_COURSES));

        $TEXT = htmlspecialchars(Translation :: get('CourseSubscribe'));
        $IMG = Theme :: getInstance()->getCommonImagePath() . 'action_subscribe.png';
        $result[] = compact('HREF', 'TEXT', 'IMG');

        $HREF = $this->get_course_action_url(
            \Chamilo\Application\Weblcms\Manager :: ACTION_COURSE_MANAGER,
            array(
                \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager :: ACTION_BROWSE_SUBSCRIBED_COURSES));

        $TEXT = htmlspecialchars(Translation :: get('CourseUnsubscribe'));
        $IMG = Theme :: getInstance()->getCommonImagePath() . 'action_unsubscribe.png';
        $result[] = compact('HREF', 'TEXT', 'IMG');

        return $result;
    }

    public function get_platform_admin_menu()
    {
        $result = array();

        $HREF = $this->get_course_action_url(
            \Chamilo\Application\Weblcms\Manager :: ACTION_COURSE_MANAGER,
            array(
                \Chamilo\Application\Weblcms\Course\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager :: ACTION_QUICK_CREATE));
        $TEXT = htmlspecialchars(Translation :: get('CourseCreate'));
        $IMG = Theme :: getInstance()->getCommonImagePath() . 'action_create.png';
        $result[] = compact('HREF', 'TEXT', 'IMG');

        $HREF = $this->get_course_action_url(\Chamilo\Application\Weblcms\Manager :: ACTION_COURSE_MANAGER);
        $TEXT = htmlspecialchars(Translation :: get('CourseList'));
        $IMG = Theme :: getInstance()->getCommonImagePath() . 'action_browser.png';
        $result[] = compact('HREF', 'TEXT', 'IMG');

        $HREF = $this->get_course_action_url(\Chamilo\Application\Weblcms\Manager :: ACTION_ADMIN_REQUEST_BROWSER);
        $TEXT = htmlspecialchars(
            Translation :: get('UserRequestList', null, \Chamilo\Application\Weblcms\Manager :: context()));
        $IMG = Theme :: getInstance()->getCommonImagePath() . 'action_browser.png';
        $result[] = compact('HREF', 'TEXT', 'IMG');

        $HREF = $this->get_course_action_url(\Chamilo\Application\Weblcms\Manager :: ACTION_REQUEST);
        $TEXT = htmlspecialchars(
            Translation :: get('RequestList', null, \Chamilo\Application\Weblcms\Manager :: context()));
        $IMG = Theme :: getInstance()->getCommonImagePath() . 'action_browser.png';
        $result[] = compact('HREF', 'TEXT', 'IMG');

        return $result;
    }

    public function get_course_action_url($action, $params = array())
    {
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager :: context();
        $params[\Chamilo\Application\Weblcms\Manager :: PARAM_ACTION] = $action;
        return htmlspecialchars(Redirect :: get_link($params));
    }
}
