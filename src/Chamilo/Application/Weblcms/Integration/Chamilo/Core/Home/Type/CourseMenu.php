<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\CourseType\Storage\DataManager as CourseTypeDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\String\SimpleTemplate;

/**
 * Block that displays main course's actions available in the main course menu.
 * That is create course,
 * register/unregister to course, etc. Do not display less common actions such as manage categories.
 *
 * @copyright (c) 2011 University of Geneva
 * @license       GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author        lopprecht
 */
class CourseMenu extends Block
{

    public function displayAdminMenu($template)
    {
        $result = [];

        if ($this->getUser()->is_platform_admin())
        {
            $menu = $this->getPlatformAdminMenu();
            $result[] = SimpleTemplate::all($template, $menu);
            $result[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
        }
        else
        {
            if ($menu = $this->getCreateCourseMenu())
            {
                $result[] = SimpleTemplate::all($template, $menu);
            }
        }

        return implode(PHP_EOL, $result);
    }

    public function displayContent()
    {
        $html = [];
        $html[] = '<div class="tool_menu">';
        $html[] = '<ul style="padding: 0; margin: 0">';
        $html[] = '{$ADMIN_MENU}';
        $html[] = '{$USER_MENU}';
        $html[] = '</ul>';
        $html[] = '</div>';

        $target = $this->getLinkTarget();

        $template = '<li class="rss_feed_icon" style="list-style-type: none;">{$GLYPH}
        <a style="top: -3px; position: relative;" href="{$HREF}" target="' . $target . '">{$TEXT}</a></li>';

        $ADMIN_MENU = $this->displayAdminMenu($template);
        $USER_MENU = SimpleTemplate::all($template, $this->getEditCourseMenu());

        $this->displayAdminMenu($template);
        SimpleTemplate::all($template, $this->getEditCourseMenu());

        return SimpleTemplate::ex($html, ['ADMIN_MENU' => $ADMIN_MENU, 'USER_MENU' => $USER_MENU]);
    }

    public function getCourseActionUrl($action, $params = [])
    {
        $params[Manager::PARAM_CONTEXT] = Manager::CONTEXT;
        $params[Manager::PARAM_ACTION] = $action;

        return htmlspecialchars($this->getUrlGenerator()->fromParameters($params));
    }

    public function getCreateCourseMenu()
    {
        if (!$this->isTeacher())
        {
            return '';
        }

        $result = [];

        $course_management_rights = CourseManagementRights::getInstance();

        $count_direct = $count_request = 0;

        $course_types = CourseTypeDataManager::retrieve_active_course_types();

        foreach ($course_types as $course_type)
        {
            if ($course_management_rights->is_allowed_management(
                CourseManagementRights::CREATE_COURSE_RIGHT, $course_type->get_id(),
                CourseManagementRights::TYPE_COURSE_TYPE
            ))
            {
                $count_direct ++;
            }
            elseif ($course_management_rights->is_allowed_management(
                CourseManagementRights::REQUEST_COURSE_RIGHT, $course_type->get_id(),
                CourseManagementRights::TYPE_COURSE_TYPE
            ))
            {
                $count_request ++;
            }
        }

        $allowCourseCreationWithoutCourseType = Configuration::getInstance()->get_setting(
            ['Chamilo\Application\Weblcms', 'allow_course_creation_without_coursetype']
        );

        if ($allowCourseCreationWithoutCourseType)
        {
            $count_direct ++;
        }

        if ($count_direct)
        {
            $href = $this->getCourseActionUrl(
                Manager::ACTION_COURSE_MANAGER, [
                    \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_QUICK_CREATE
                ]
            );
            $TEXT = htmlspecialchars(Translation::get('CourseCreate'));
            $glyph = new FontAwesomeGlyph('plus');
            $GLYPH_RENDER = $glyph->render();
            $result[] = compact('HREF', 'TEXT', 'GLYPH_RENDER');
        }

        if ($count_request)
        {
            $HREF = $this->getUrl(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_REQUEST,
                    \Chamilo\Application\Weblcms\Request\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Request\Manager::ACTION_CREATE
                ]
            );

            $TEXT = htmlspecialchars(Translation::get('CourseRequest'));
            $GLYPH = new FontAwesomeGlyph('plus');
            $GLYPH_RENDER = $glyph->render();
            $result[] = compact('HREF', 'TEXT', 'GLYPH_RENDER');
        }

        return $result;
    }

    public function getEditCourseMenu()
    {
        $result = [];

        $HREF = $this->getCourseActionUrl(
            Manager::ACTION_COURSE_MANAGER, [
                \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_BROWSE_UNSUBSCRIBED_COURSES
            ]
        );

        $TEXT = htmlspecialchars(Translation::get('CourseSubscribe'));
        $glyph = new FontAwesomeGlyph('plus-circle');
        $GLYPH_RENDER = $glyph->render();
        $result[] = compact('HREF', 'TEXT', 'GLYPH_RENDER');

        $HREF = $this->getCourseActionUrl(
            Manager::ACTION_COURSE_MANAGER, [
                \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_BROWSE_SUBSCRIBED_COURSES
            ]
        );

        $TEXT = htmlspecialchars(Translation::get('CourseUnsubscribe'));
        $glyph = new FontAwesomeGlyph('minus-square');
        $GLYPH_RENDER = $glyph->render();
        $result[] = compact('HREF', 'TEXT', 'GLYPH_RENDER');

        return $result;
    }

    public function getPlatformAdminMenu()
    {
        $result = [];

        $HREF = $this->getCourseActionUrl(
            Manager::ACTION_COURSE_MANAGER, [
                \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_QUICK_CREATE
            ]
        );
        $TEXT = htmlspecialchars(Translation::get('CourseCreate'));
        $glyph = new FontAwesomeGlyph('plus');
        $GLYPH_RENDER = $glyph->render();
        $result[] = compact('HREF', 'TEXT', 'GLYPH_RENDER');

        $HREF = $this->getCourseActionUrl(Manager::ACTION_COURSE_MANAGER);
        $TEXT = htmlspecialchars(Translation::get('CourseList'));
        $glyph = new FontAwesomeGlyph('folder');
        $GLYPH_RENDER = $glyph->render();
        $result[] = compact('HREF', 'TEXT', 'GLYPH_RENDER');

        $HREF = $this->getCourseActionUrl(Manager::ACTION_ADMIN_REQUEST_BROWSER);
        $TEXT = htmlspecialchars(
            Translation::get('UserRequestList', null, Manager::CONTEXT)
        );
        $glyph = new FontAwesomeGlyph('folder');
        $GLYPH_RENDER = $glyph->render();
        $result[] = compact('HREF', 'TEXT', 'GLYPH_RENDER');

        $HREF = $this->getCourseActionUrl(Manager::ACTION_REQUEST);
        $TEXT = htmlspecialchars(Translation::get('RequestList', null, Manager::CONTEXT));
        $glyph = new FontAwesomeGlyph('folder');
        $GLYPH_RENDER = $glyph->render();
        $result[] = compact('HREF', 'TEXT', 'GLYPH_RENDER');

        return $result;
    }

    public function isTeacher()
    {
        return parent::getUser()->get_status(User::STATUS_TEACHER);
    }
}
