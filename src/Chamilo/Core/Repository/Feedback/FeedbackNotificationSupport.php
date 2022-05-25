<?php
namespace Chamilo\Core\Repository\Feedback;

use Chamilo\Core\Repository\Feedback\Infrastructure\Service\NotificationHandlerInterface;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Notification;

/**
 * Interface which indicates a component implements the Feedback manager
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface FeedbackNotificationSupport
{

    /**
     * Retrieve the Notification instance for the current user
     * 
     * @return Notification
     */
    public function retrieve_notification();

    /**
     * Retrieves all the notifications
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection<Notification>
     */
    public function retrieve_notifications();

    /**
     * Returns an array of notification handlers
     * 
     * @return NotificationHandlerInterface[]
     */
    public function get_notification_handlers();

    /**
     * Returns an newly instantiated Notification object
     * 
     * @return Notification
     */
    public function get_notification();
}
