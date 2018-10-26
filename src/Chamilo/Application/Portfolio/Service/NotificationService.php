<?php
namespace Chamilo\Application\Portfolio\Service;

use Chamilo\Application\Portfolio\Storage\Repository\NotificationRepository;

/**
 *
 * @package Chamilo\Application\Portfolio\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class NotificationService
{

    /**
     *
     * @var \Chamilo\Application\Portfolio\Storage\Repository\NotificationRepository
     */
    private $notificationRepository;

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\NotificationRepository $notificationRepository
     */
    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     *
     * @return \Chamilo\Application\Portfolio\Storage\Repository\NotificationRepository
     */
    public function getNotificationRepository()
    {
        return $this->notificationRepository;
    }

    /**
     *
     * @param \Chamilo\Application\Portfolio\Storage\Repository\NotificationRepository $notificationRepository
     */
    public function setNotificationRepository(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }
}

