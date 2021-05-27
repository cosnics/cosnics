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
    const TYPE_DANGER = 'danger';
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';

    /**
     * Renders one or more notification messages
     *
     * @param \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage|\Chamilo\Libraries\Format\NotificationMessage\NotificationMessage[] $notificationMessages
     *
     * @return string
     */
    public function render($notificationMessages = [])
    {
        if (empty($notificationMessages))
        {
            return '';
        }

        if ($notificationMessages instanceof NotificationMessage)
        {
            $notificationMessages = array($notificationMessages);
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

    /**
     * Renders a given notification message
     *
     * @param \Chamilo\Libraries\Format\NotificationMessage\NotificationMessage $notificationMessage
     *
     * @return string
     */
    protected function renderNotificationMessage(NotificationMessage $notificationMessage)
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
}
