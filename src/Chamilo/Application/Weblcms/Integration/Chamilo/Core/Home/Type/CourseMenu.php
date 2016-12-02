<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\CourseType\Storage\DataManager as CourseTypeDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
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

    public function isTeacher()
    {
        return parent::getUser()->get_status(User::STATUS_TEACHER);
    }

    public function displayContent()
    {
        $html = array();
        $html[] = '<div class="tool_menu">';
        $html[] = '<ul style="padding: 0; margin: 0">';
        $html[] = '{$ADMIN_MENU}';
        $html[] = '{$USER_MENU}';
        $html[] = '</ul>';
        $html[] = '</div>';
        
        $target = $this->getLinkTarget();
        
        $template = '<li class="rss_feed_icon" style="background-repeat: no-repeat; list-style-type: none; padding-left: 25px; margin: 0; background-image: url({$IMG})">
        <a style="top: -3px; position: relative;" href="{$HREF}" target="' . $target .
             '">{$TEXT}</a></li>';
        
        $ADMIN_MENU = $this->displayAdminMenu($template);
        $USER_MENU = SimpleTemplate::all($template, $this->getEditCourseMenu());
        
        $this->displayAdminMenu($template);
        SimpleTemplate::all($template, $this->getEditCourseMenu());
        
        return SimpleTemplate::ex($html, array('ADMIN_MENU' => $ADMIN_MENU, 'USER_MENU' => $USER_MENU));
    }

    public function displayAdminMenu($template)
    {
        $result = array();
        
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

    public function getCreateCourseMenu()
    {
        if (! $this->isTeacher())
        {
            return '';
        }
        
        $result = array();
        
        $course_management_rights = \Chamilo\Application\Weblcms\Rights\CourseManagementRights::getInstance();
        
        $count_direct = $count_request = 0;
        
        $course_types = CourseTypeDataManager::retrieve_active_course_types();
        
        while ($course_type = $course_types->next_result())
        {
            if ($course_management_rights->is_allowed(
                \Chamilo\Application\Weblcms\Rights\CourseManagementRights::CREATE_COURSE_RIGHT, 
                $course_type->get_id(), 
                \Chamilo\Application\Weblcms\Rights\CourseManagementRights::TYPE_COURSE_TYPE))
            {
                $count_direct ++;
            }
            elseif ($course_management_rights->is_allowed(
                \Chamilo\Application\Weblcms\Rights\CourseManagementRights::REQUEST_COURSE_RIGHT, 
                $course_type->get_id(), 
                \Chamilo\Application\Weblcms\Rights\CourseManagementRights::TYPE_COURSE_TYPE))
            {
                $count_request ++;
            }
        }
        
        $allowCourseCreationWithoutCourseType = Configuration::getInstance()->get_setting(
            array('Chamilo\Application\Weblcms', 'allow_course_creation_without_coursetype'));
        
        if ($allowCourseCreationWithoutCourseType)
        {
            $count_direct ++;
        }
        
        if ($count_direct)
        {
            $href = $this->getCourseActionUrl(
                \Chamilo\Application\Weblcms\Manager::ACTION_COURSE_MANAGER, 
                array(
                    \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_QUICK_CREATE));
            $TEXT = htmlspecialchars(Translation::get('CourseCreate'));
            $IMG = Theme::getInstance()->getCommonImagePath('Action/Create');
            $result[] = compact('HREF', 'TEXT', 'IMG');
        }
        
        if ($count_request)
        {
            $HREF = $this->getUrl(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::context(), 
                    Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_REQUEST, 
                    \Chamilo\Application\Weblcms\Request\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Request\Manager::ACTION_CREATE));
            
            $TEXT = htmlspecialchars(Translation::get('CourseRequest'));
            $IMG = Theme::getInstance()->getCommonImagePath('Action/Create');
            $result[] = compact('HREF', 'TEXT', 'IMG');
        }
        
        return $result;
    }

    public function getEditCourseMenu()
    {
        $result = array();
        
        $HREF = $this->getCourseActionUrl(
            \Chamilo\Application\Weblcms\Manager::ACTION_COURSE_MANAGER, 
            array(
                \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_BROWSE_UNSUBSCRIBED_COURSES));
        
        $TEXT = htmlspecialchars(Translation::get('CourseSubscribe'));
        $IMG = Theme::getInstance()->getCommonImagePath('Action/Subscribe');
        $result[] = compact('HREF', 'TEXT', 'IMG');
        
        $HREF = $this->getCourseActionUrl(
            \Chamilo\Application\Weblcms\Manager::ACTION_COURSE_MANAGER, 
            array(
                \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_BROWSE_SUBSCRIBED_COURSES));
        
        $TEXT = htmlspecialchars(Translation::get('CourseUnsubscribe'));
        $IMG = Theme::getInstance()->getCommonImagePath('Action/Unsubscribe');
        $result[] = compact('HREF', 'TEXT', 'IMG');
        
        return $result;
    }

    public function getPlatformAdminMenu()
    {
        $result = array();
        
        $HREF = $this->getCourseActionUrl(
            \Chamilo\Application\Weblcms\Manager::ACTION_COURSE_MANAGER, 
            array(
                \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_QUICK_CREATE));
        $TEXT = htmlspecialchars(Translation::get('CourseCreate'));
        $IMG = Theme::getInstance()->getCommonImagePath('Action/Create');
        $result[] = compact('HREF', 'TEXT', 'IMG');
        
        $HREF = $this->getCourseActionUrl(\Chamilo\Application\Weblcms\Manager::ACTION_COURSE_MANAGER);
        $TEXT = htmlspecialchars(Translation::get('CourseList'));
        $IMG = Theme::getInstance()->getCommonImagePath('Action/Browser');
        $result[] = compact('HREF', 'TEXT', 'IMG');
        
        $HREF = $this->getCourseActionUrl(\Chamilo\Application\Weblcms\Manager::ACTION_ADMIN_REQUEST_BROWSER);
        $TEXT = htmlspecialchars(
            Translation::get('UserRequestList', null, \Chamilo\Application\Weblcms\Manager::context()));
        $IMG = Theme::getInstance()->getCommonImagePath('Action/Browser');
        $result[] = compact('HREF', 'TEXT', 'IMG');
        
        $HREF = $this->getCourseActionUrl(\Chamilo\Application\Weblcms\Manager::ACTION_REQUEST);
        $TEXT = htmlspecialchars(Translation::get('RequestList', null, \Chamilo\Application\Weblcms\Manager::context()));
        $IMG = Theme::getInstance()->getCommonImagePath('Action/Browser');
        $result[] = compact('HREF', 'TEXT', 'IMG');
        
        return $result;
    }

    public function getCourseActionUrl($action, $params = array())
    {
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_CONTEXT] = \Chamilo\Application\Weblcms\Manager::context();
        $params[\Chamilo\Application\Weblcms\Manager::PARAM_ACTION] = $action;
        
        $redirect = new Redirect($params);
        return htmlspecialchars($redirect->getUrl());
    }
}
