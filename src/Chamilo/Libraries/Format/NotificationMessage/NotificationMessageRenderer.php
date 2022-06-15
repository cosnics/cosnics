<?php
namespace Chamilo\Libraries\Format\NotificationMessage;

/**
 * Renders notification messages
 *
 * @package Chamilo\Libraries\Format\NotificationMessage
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationMessageRenderer
{
    public const TYPE_DANGER = 'danger';
    public const TYPE_INFO = 'info';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_WARNING = 'warning';

    /**
     * @param \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage[] $notificationMessages
     */
    public function render(array $notificationMessages = []): string
    {
        if (empty($notificationMessages))
        {
            return '';
        }

        $html = [];

        $html[] = '<div class="notifications">';

        foreach ($notificationMessages as $notificationMessage)
        {
            $html[] = $this->renderNotificationMessage($notificationMessage);
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    protected function renderNotificationMessage(NotificationMessage $notificationMessage): string
    {
        $html = [];

        $html[] = '<div class="alert alert-' . $notificationMessage->getType() . ' alert-dismissible" role="alert">';
        $html[] = '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        $html[] = '<span aria-hidden="true">&times;</span>';
        $html[] = '</button>';
        $html[] = $notificationMessage->getMessage();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderOne(NotificationMessage $notificationMessage): string
    {
        return $this->render([$notificationMessage]);
    }
}
