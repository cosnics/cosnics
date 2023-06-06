<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager as CourseTypeDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Notification\Manager;
use Chamilo\Core\Notification\Service\NotificationManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentNotifications extends Block implements ConfigurableInterface, StaticBlockTitleInterface
{
    public const CONFIGURATION_COURSE_TYPE = 'course_type';

    /**
     * The cached course type id
     *
     * @var int
     */
    protected $courseTypeId;

    /**
     * @var bool
     */
    protected $settingsLoaded;

    /**
     * The cached user course category id
     *
     * @var int
     */
    protected $userCourseCategoryId;

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function displayContent()
    {
        $this->loadSettings();

        $retrieveNotificationsUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax\Manager::CONTEXT,
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax\Manager::ACTION_GET_ASSIGNMENT_NOTIFICATIONS,
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax\Manager::PARAM_COURSE_TYPE_ID => $this->courseTypeId,
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax\Manager::PARAM_USER_COURSE_CATEGORY_ID => $this->userCourseCategoryId
            ]
        );

        $viewNotificationUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_VIEW_NOTIFICATION,
                Manager::PROPERTY_NOTIFICATION_ID => '__NOTIFICATION_ID__'
            ]
        );

        return $this->getTwig()->render(
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home:AssignmentNotifications.html.twig', [
                'RETRIEVE_NOTIFICATIONS_URL' => $retrieveNotificationsUrl,
                'VIEW_NOTIFICATION_URL' => $viewNotificationUrl,
                'BLOCK_ID' => $this->getBlock()->getId(),
                'HIDDEN' => !$this->getBlock()->isVisible(),
                'ADMIN_EMAIL' => $this->getConfigurationConsulter()->getSetting(
                    ['Chamilo\Core\Admin', 'administrator_email']
                )
            ]
        );
    }

    /**
     * @see \Chamilo\Core\Home\Architecture\ConfigurableInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables()
    {
        return [self::CONFIGURATION_COURSE_TYPE];
    }

    /**
     * Returns the selected course type id
     *
     * @return int
     */
    public function getCourseTypeId()
    {
        return $this->courseTypeId;
    }

    /**
     * @return \Chamilo\Core\Notification\Service\NotificationManager
     */
    protected function getNotificationManager()
    {
        return $this->getService(NotificationManager::class);
    }

    /**
     * Returns the block's title to display.
     *
     * @return string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTitle()
    {
        $this->loadSettings();

        $title = '<span style="display: flex; align-items: center;">' . $this->getTranslator()->trans(
                'AssignmentNotifications', [], 'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home'
            );

        $notificationCount = $this->getNotificationManager()->countUnseenNotificationsByContextPathForUser(
            'Assignment', $this->get_user()
        );

        if ($notificationCount > 0)
        {
            $title .= '<span class="notifications-block-new-label">' . $notificationCount . '</span>';
        }

        $title .= ' (' . $this->getTitleForCourseTypeAndCourseCategory() . ')';
        $title .= '</span>';

        return $title;
    }

    protected function getTitleForCourseTypeAndCourseCategory()
    {
        $course_type_id = $this->getCourseTypeId();

        if ($course_type_id > 0)
        {
            $course_type = CourseTypeDataManager::retrieve_by_id(CourseType::class, $course_type_id);

            if ($course_type)
            {
                $course_type_title = $course_type->get_title();
            }
            else
            {
                return Translation::get('NoSuchCourseType');
            }
        }
        elseif ($course_type_id === 0)
        {
            $course_type_title = Translation::get('NoCourseType');
        }
        else
        {
            $course_type_title = Translation::get('AllCourses');
        }

        $user_course_category_id = $this->getUserCourseCategoryId();

        if ($user_course_category_id > 0)
        {

            $course_user_category = DataManager::retrieve_by_id(
                CourseUserCategory::class, $user_course_category_id
            );

            if ($course_user_category)
            {
                $course_user_category_title = ' - ' . $course_user_category->get_title();
            }
        }

        return $course_type_title . $course_user_category_title;
    }

    /**
     * Returns the selected user course category id (if any)
     *
     * @return int
     */
    public function getUserCourseCategoryId()
    {
        return $this->userCourseCategoryId;
    }

    /**
     * Loads the settings of this block
     */
    private function loadSettings()
    {
        if ($this->settingsLoaded)
        {
            return;
        }

        $courseTypeIds = json_decode($this->getBlock()->getSetting(self::CONFIGURATION_COURSE_TYPE));

        if (!is_array($courseTypeIds))
        {
            $courseTypeIds = [$courseTypeIds];
        }

        $this->courseTypeId = $courseTypeIds[0];
        $this->userCourseCategoryId = $courseTypeIds[1];
        $this->settingsLoaded = true;
    }

    /**
     * @see \Chamilo\Core\Home\Renderer\BlockRenderer::renderContentFooter()
     */
    public function renderContentFooter()
    {
    }

    /**
     * @see \Chamilo\Core\Home\Renderer\BlockRenderer::renderContentHeader()
     */
    public function renderContentHeader()
    {
    }
}