<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\CourseType\Storage\DataManager as CourseTypeDataManager;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\CourseManagementRights;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Utilities\String\SimpleTemplate;

/**
 * Block that displays main course's actions available in the main course menu.
 * That is create course,
 * register/unregister to course, etc. Do not display less common actions such as manage categories.
 *
 * @package       Chamilo\Application\Weblcms\Service\Home
 * @copyright     2011 University of Geneva
 * @license       GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author        lopprecht
 * @author        Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CourseMenuBlockRenderer extends BlockRenderer
{
    public const CONTEXT = Manager::CONTEXT;

    public function displayAdminMenu($template, ?User $user = null): string
    {
        $result = [];

        if ($user->isPlatformAdmin())
        {
            $menu = $this->getPlatformAdminMenu();
            $result[] = SimpleTemplate::all($template, $menu);
            $result[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
        }
        elseif ($menu = $this->getCreateCourseMenu($user))
        {
            $result[] = SimpleTemplate::all($template, $menu);
        }

        return implode(PHP_EOL, $result);
    }

    public function displayContent(Element $block, ?User $user = null): string
    {
        $html = [];
        $html[] = '<div class="tool_menu">';
        $html[] = '<ul style="padding: 0; margin: 0">';
        $html[] = '{$ADMIN_MENU}';
        $html[] = '{$USER_MENU}';
        $html[] = '</ul>';
        $html[] = '</div>';

        $template = '<li class="rss_feed_icon" style="list-style-type: none;">{$GLYPH}
        <a style="top: -3px; position: relative;" href="{$HREF}" target="__blank">{$TEXT}</a></li>';

        $adminMenu = $this->displayAdminMenu($template, $user);
        $userMenu = SimpleTemplate::all($template, $this->getEditCourseMenu());

        $this->displayAdminMenu($template);
        SimpleTemplate::all($template, $this->getEditCourseMenu());

        return SimpleTemplate::ex($html, ['ADMIN_MENU' => $adminMenu, 'USER_MENU' => $userMenu]);
    }

    public function getCourseActionUrl($action, $params = []): string
    {
        $params[Application::PARAM_CONTEXT] = Manager::CONTEXT;
        $params[Application::PARAM_ACTION] = $action;

        return htmlspecialchars($this->getUrlGenerator()->fromParameters($params));
    }

    public function getCreateCourseMenu(?User $user = null): array|string
    {
        if (!$this->isTeacher($user))
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
                CourseManagementRights::CREATE_COURSE_RIGHT, $course_type->getId(), WeblcmsRights::TYPE_COURSE_TYPE
            ))
            {
                $count_direct ++;
            }
            elseif ($course_management_rights->is_allowed_management(
                CourseManagementRights::REQUEST_COURSE_RIGHT, $course_type->getId(), WeblcmsRights::TYPE_COURSE_TYPE
            ))
            {
                $count_request ++;
            }
        }

        $allowCourseCreationWithoutCourseType = $this->getConfigurationConsulter()->getSetting(
            ['Chamilo\Application\Weblcms', 'allow_course_creation_without_coursetype']
        );

        if ($allowCourseCreationWithoutCourseType)
        {
            $count_direct ++;
        }

        $translator = $this->getTranslator();

        if ($count_direct)
        {
            $courseActionUrl = $this->getCourseActionUrl(
                Manager::ACTION_COURSE_MANAGER, [
                    \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_QUICK_CREATE
                ]
            );
            $label = htmlspecialchars($translator->trans('CourseCreate', [], Manager::CONTEXT));
            $glyph = new FontAwesomeGlyph('plus');

            $result[] = ['HREF' => $courseActionUrl, 'TEXT' => $label, 'GLYPH_RENDER' => $glyph->render()];
        }

        if ($count_request)
        {
            $requestUrl = $this->getUrlGenerator()->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Application::PARAM_ACTION => Manager::ACTION_REQUEST,
                    \Chamilo\Application\Weblcms\Request\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Request\Manager::ACTION_CREATE
                ]
            );

            $label = htmlspecialchars($translator->trans('CourseRequest', [], Manager::CONTEXT));

            $glyph = new FontAwesomeGlyph('plus');

            $result[] = ['HREF' => $requestUrl, 'TEXT' => $label, 'GLYPH_RENDER' => $glyph->render()];
        }

        return $result;
    }

    public function getEditCourseMenu(): array
    {
        $translator = $this->getTranslator();

        $result = [];

        $browseUnsubscribedCoursesUrl = $this->getCourseActionUrl(
            Manager::ACTION_COURSE_MANAGER, [
                \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_BROWSE_UNSUBSCRIBED_COURSES
            ]
        );

        $label = htmlspecialchars($translator->trans('CourseSubscribe', [], Manager::CONTEXT));
        $glyph = new FontAwesomeGlyph('plus-circle');

        $result[] = ['HREF' => $browseUnsubscribedCoursesUrl, 'TEXT' => $label, 'GLYPH_RENDER' => $glyph->render()];

        $browseSubscribedCoursesUrl = $this->getCourseActionUrl(
            Manager::ACTION_COURSE_MANAGER, [
                \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_BROWSE_SUBSCRIBED_COURSES
            ]
        );

        $label = htmlspecialchars($translator->trans('CourseUnsubscribe', [], Manager::CONTEXT));
        $glyph = new FontAwesomeGlyph('minus-square');

        $result[] = ['HREF' => $browseSubscribedCoursesUrl, 'TEXT' => $label, 'GLYPH_RENDER' => $glyph->render()];

        return $result;
    }

    public function getPlatformAdminMenu(): array
    {
        $translator = $this->getTranslator();

        $result = [];

        $quickCreateUrl = $this->getCourseActionUrl(
            Manager::ACTION_COURSE_MANAGER, [
                \Chamilo\Application\Weblcms\Course\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Course\Manager::ACTION_QUICK_CREATE
            ]
        );
        $label = htmlspecialchars($translator->trans('CourseCreate', [], Manager::CONTEXT));
        $glyph = new FontAwesomeGlyph('plus');

        $result[] = ['HREF' => $quickCreateUrl, 'TEXT' => $label, 'GLYPH_RENDER' => $glyph->render()];

        $courseManagerUrl = $this->getCourseActionUrl(Manager::ACTION_COURSE_MANAGER);
        $label = htmlspecialchars($translator->trans('CourseList', [], Manager::CONTEXT));
        $glyph = new FontAwesomeGlyph('folder');

        $result[] = ['HREF' => $courseManagerUrl, 'TEXT' => $label, 'GLYPH_RENDER' => $glyph->render()];

        $requestBrowserUrl = $this->getCourseActionUrl(Manager::ACTION_ADMIN_REQUEST_BROWSER);
        $label = htmlspecialchars(
            $translator->trans('UserRequestList', [], Manager::CONTEXT)
        );
        $glyph = new FontAwesomeGlyph('folder');

        $result[] = ['HREF' => $requestBrowserUrl, 'TEXT' => $label, 'GLYPH_RENDER' => $glyph->render()];

        $requestUrl = $this->getCourseActionUrl(Manager::ACTION_REQUEST);
        $label = htmlspecialchars($translator->trans('RequestList', [], Manager::CONTEXT));
        $glyph = new FontAwesomeGlyph('folder');

        $result[] = ['HREF' => $requestUrl, 'TEXT' => $label, 'GLYPH_RENDER' => $glyph->render()];

        return $result;
    }

    public function isTeacher(?User $user = null): bool
    {
        return $user->get_status() == User::STATUS_TEACHER;
    }
}
