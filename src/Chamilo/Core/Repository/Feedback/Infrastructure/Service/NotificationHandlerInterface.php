<?php
namespace Chamilo\Core\Repository\Feedback\Infrastructure\Service;

use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;

/**
 * Interface for notification handlers.
 * By using this interface one (or more) notification handler strategies can be
 * used to sent notifications about new feedback
 * 
 * @package Chamilo\Core\Repository\Feedback\Infrastructure\Service
 */
interface NotificationHandlerInterface
{

    /**
     * Handles a single notification for a new feedback object
     * 
     * @param Feedback $feedback
     * @param Notification[] $notifications
     */
    public function handleNotifications(Feedback $feedback, array $notifications = array());
}