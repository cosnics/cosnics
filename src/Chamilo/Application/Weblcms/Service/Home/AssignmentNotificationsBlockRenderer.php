<?php
namespace Chamilo\Application\Weblcms\Service\Home;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager as CourseTypeDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseUserCategory;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Notification\Manager;
use Chamilo\Core\Notification\Service\NotificationManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

/**
 * @package Chamilo\Application\Weblcms\Service\Home
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentNotificationsBlockRenderer extends BlockRenderer
    implements ConfigurableBlockInterface, StaticBlockTitleInterface
{
    public const CONFIGURATION_COURSE_TYPE = 'course_type';

    protected NotificationManager $notificationManager;

    protected Environment $twigEnvironment;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter, NotificationManager $notificationManager,
        Environment $twigEnvironment
    )
    {
        parent::__construct($homeService, $urlGenerator, $translator, $configurationConsulter);

        $this->notificationManager = $notificationManager;
        $this->twigEnvironment = $twigEnvironment;
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function displayContent(Block $block, ?User $user = null): string
    {
        $retrieveNotificationsUrl = $this->getUrlGenerator()->fromParameters(
            [
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Ajax\Manager::CONTEXT,
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Ajax\Manager::ACTION_GET_ASSIGNMENT_NOTIFICATIONS,
                \Chamilo\Application\Weblcms\Ajax\Manager::PARAM_COURSE_TYPE_ID => $this->getCourseTypeId($block),
                \Chamilo\Application\Weblcms\Ajax\Manager::PARAM_USER_COURSE_CATEGORY_ID => $this->getUserCourseCategoryId(
                    $block
                )
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
            'Chamilo\Application\Weblcms:AssignmentNotifications.html.twig', [
                'RETRIEVE_NOTIFICATIONS_URL' => $retrieveNotificationsUrl,
                'VIEW_NOTIFICATION_URL' => $viewNotificationUrl,
                'BLOCK_ID' => $block->getId(),
                'HIDDEN' => !$block->isVisible(),
                'ADMIN_EMAIL' => $this->getConfigurationConsulter()->getSetting(
                    ['Chamilo\Core\Admin', 'administrator_email']
                )
            ]
        );
    }

    /**
     * @see \Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables(): array
    {
        return [self::CONFIGURATION_COURSE_TYPE];
    }

    protected function getCourseTypeConfiguration(Block $block): array
    {
        $courseTypeIds = json_decode($block->getSetting(self::CONFIGURATION_COURSE_TYPE));

        if (!is_array($courseTypeIds))
        {
            $courseTypeIds = [$courseTypeIds];
        }

        return $courseTypeIds;
    }

    protected function getCourseTypeId(Block $block): int
    {
        $courseTypeIds = $this->getCourseTypeConfiguration($block);

        return (int) $courseTypeIds[0];
    }

    protected function getNotificationManager(): NotificationManager
    {
        return $this->notificationManager;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTitle(Block $block, ?User $user = null): string
    {
        $title = '<span style="display: flex; align-items: center;">' . $this->getTranslator()->trans(
                'AssignmentNotifications', [], 'Chamilo\Application\Weblcms'
            );

        $notificationCount = $this->getNotificationManager()->countUnseenNotificationsByContextPathForUser(
            'Assignment', $user
        );

        if ($notificationCount > 0)
        {
            $title .= '<span class="notifications-block-new-label">' . $notificationCount . '</span>';
        }

        $title .= ' (' . $this->getTitleForCourseTypeAndCourseCategory($block) . ')';
        $title .= '</span>';

        return $title;
    }

    protected function getTitleForCourseTypeAndCourseCategory(Block $block): string
    {
        $translator = $this->getTranslator();
        $course_type_id = $this->getCourseTypeId($block);

        if ($course_type_id > 0)
        {
            $course_type = CourseTypeDataManager::retrieve_by_id(CourseType::class, $course_type_id);

            if ($course_type)
            {
                $course_type_title = $course_type->get_title();
            }
            else
            {
                return $translator->trans('NoSuchCourseType', [], Manager::CONTEXT);
            }
        }
        elseif ($course_type_id === 0)
        {
            $course_type_title = $translator->trans('NoCourseType', [], Manager::CONTEXT);
        }
        else
        {
            $course_type_title = $translator->trans('AllCourses', [], Manager::CONTEXT);
        }

        $user_course_category_id = $this->getUserCourseCategoryId($block);

        if ($user_course_category_id > 0)
        {

            $course_user_category = DataManager::retrieve_by_id(
                CourseUserCategory::class, $user_course_category_id
            );

            if ($course_user_category)
            {
                return $course_type_title . ' - ' . $course_user_category->get_title();
            }
        }

        return $course_type_title;
    }

    protected function getTwig(): Environment
    {
        return $this->twigEnvironment;
    }

    protected function getUserCourseCategoryId(Block $block): int
    {
        $courseTypeIds = $this->getCourseTypeConfiguration($block);

        return (int) $courseTypeIds[1];
    }

    public function renderContentFooter(Block $block): string
    {
        return '';
    }

    public function renderContentHeader(Block $block): string
    {
        return '';
    }
}