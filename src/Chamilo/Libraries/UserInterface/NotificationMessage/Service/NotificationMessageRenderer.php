<?php
namespace Chamilo\Libraries\UserInterface\NotificationMessage\Service;

use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;

/**
 * Renders notification messages
 *
 * @package Chamilo\Libraries\UserInterface\NotificationMessage\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationMessageRenderer
{
    public const TYPE_DANGER = 'danger';
    public const TYPE_INFO = 'info';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_WARNING = 'warning';

    /**
     * @param \Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage[] $notificationMessages
     */
    public function render(array $notificationMessages = [], bool $addcontainer = true): string
    {
        if (empty($notificationMessages)) {
            return '';
        }

        $html = [];

        if ($addcontainer) {
            $html[] = '<div class="notifications">';
        }

        foreach ($notificationMessages as $notificationMessage) {
            $html[] = $this->renderNotificationMessage($notificationMessage);
        }

        if ($addcontainer) {
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    protected function renderNotificationMessage(NotificationMessage $notificationMessage): string
    {
        $html = [];

        $html[] = '<div class="alert alert-' . $notificationMessage->getType() . ' alert-dismissible" role="alert">';
        $html[] = $notificationMessage->getMessage();
        $html[] = '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderOne(NotificationMessage $notificationMessage, bool $addcontainer = true): string
    {
        return $this->render([$notificationMessage], $addcontainer);
    }
}
