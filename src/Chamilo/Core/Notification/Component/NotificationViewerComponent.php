<?php

namespace Chamilo\Core\Notification\Component;

use Chamilo\Core\Notification\Manager;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Core\Notification\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationViewerComponent extends Manager
{
    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    function run()
    {
        try
        {
            $notificationId = $this->getRequest()->getFromQuery(self::PROPERTY_NOTIFICATION_ID);
            $notification = $this->getNotificationManager()->getNotificationById($notificationId);
            if (!$this->getNotificationManager()->canUserViewNotification($notification, $this->getUser()))
            {
                throw new Exception(
                    sprintf('User %s can not view notification %s', $this->getUser()->getId(), $notificationId)
                );
            }
        }
        catch (Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);
            throw new NotAllowedException();
        }

        $this->getNotificationManager()->setNotificationReadForUser($notification, $this->getUser());

        return new RedirectResponse($notification->getUrl());
    }
}