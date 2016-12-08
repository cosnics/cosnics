<?php
namespace Chamilo\Core\Repository\Feedback\Infrastructure\Service;

use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;

/**
 * Service to handle notifications of new feedback
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface NotificationServiceInterface
{

    /**
     * Notify's the given users about a new feedback that has been created
     * 
     * @param Feedback $feedback
     * @param Notification[] $notifications
     */
    public function notify(Feedback $feedback, array $notifications = array());
}