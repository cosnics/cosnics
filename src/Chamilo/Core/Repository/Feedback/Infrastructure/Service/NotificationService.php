<?php
namespace Chamilo\Core\Repository\Feedback\Infrastructure\Service;

use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;

/**
 * Service to handle notifications of new feedback
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationService implements NotificationServiceInterface
{

    /**
     *
     * @var NotificationHandlerInterface
     */
    protected $notificationHandlers;

    /**
     *
     * @param NotificationHandlerInterface[] $notificationHandlers
     */
    public function __construct(array $notificationHandlers = array())
    {
        $this->notificationHandlers = $notificationHandlers;
    }

    /**
     * Adds a new notification handler
     * 
     * @param NotificationHandlerInterface $notificationHandler
     */
    public function addNotificationHandler(NotificationHandlerInterface $notificationHandler)
    {
        $this->notificationHandlers[] = $notificationHandler;
    }

    /**
     * Notify's the given users about a new feedback that has been created
     * 
     * @param Feedback $feedback
     * @param Notification[] $notifications
     */
    public function notify(Feedback $feedback, array $notifications = array())
    {
        if (empty($notifications))
        {
            return;
        }
        
        foreach ($this->notificationHandlers as $notificationHandler)
        {
            $notificationHandler->handleNotifications($feedback, $notifications);
        }
    }
}