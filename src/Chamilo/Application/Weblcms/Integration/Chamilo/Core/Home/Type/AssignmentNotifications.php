<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Block;
use Chamilo\Core\Notification\Service\NotificationManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Redirect;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Type
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentNotifications extends Block
{
    /**
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function displayContent()
    {
        $redirect = new Redirect(
            [
                Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax\Manager::context(
                ),
                Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax\Manager::ACTION_GET_ASSIGNMENT_NOTIFICATIONS
            ]
        );

        $retrieveNotificationsUrl = $redirect->getUrl();

        $redirect = new Redirect(
            [
                Application::PARAM_CONTEXT => \Chamilo\Core\Notification\Manager::context(),
                Application::PARAM_ACTION => \Chamilo\Core\Notification\Manager::ACTION_VIEW_NOTIFICATION,
                \Chamilo\Core\Notification\Manager::PROPERTY_NOTIFICATION_ID => '__NOTIFICATION_ID__'
            ]
        );

        $viewNotificationUrl = $redirect->getUrl();

        return $this->getTwig()->render(
            'Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home:AssignmentNotifications.html.twig',
            [
                'RETRIEVE_NOTIFICATIONS_URL' => $retrieveNotificationsUrl,
                'VIEW_NOTIFICATION_URL' => $viewNotificationUrl,
                'BLOCK_ID' => $this->getBlock()->getId()
            ]
        );
    }

    /**
     * Returns the block's title to display.
     *
     * @return string
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTitle()
    {
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

        $title .= '</span>';

        return $title;
    }

    /**
     *
     * @see \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer::renderContentHeader()
     */
    public function renderContentHeader()
    {
    }

    /**
     *
     * @see \Chamilo\Core\Home\Renderer\Type\Basic\BlockRenderer::renderContentFooter()
     */
    public function renderContentFooter()
    {
    }

    /**
     * @return \Chamilo\Core\Notification\Service\NotificationManager
     */
    protected function getNotificationManager()
    {
        return $this->getService(NotificationManager::class);
    }
}