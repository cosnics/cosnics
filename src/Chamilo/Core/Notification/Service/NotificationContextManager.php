<?php

namespace Chamilo\Core\Notification\Service;

use Chamilo\Core\Notification\Storage\Entity\NotificationContext;
use Chamilo\Core\Notification\Storage\Repository\NotificationContextRepository;
use RuntimeException;

/**
 * @package Chamilo\Core\Notification\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationContextManager
{
    /**
     * @var \Chamilo\Core\Notification\Storage\Repository\NotificationContextRepository
     */
    protected $notificationContextRepository;

    /**
     * @var NotificationContext[]
     */
    protected $notificationContextByPathCache = [];

    /**
     * FilterManager constructor.
     *
     * @param \Chamilo\Core\Notification\Storage\Repository\NotificationContextRepository $notificationContextRepository
     */
    public function __construct(NotificationContextRepository $notificationContextRepository)
    {
        $this->notificationContextRepository = $notificationContextRepository;
    }

    /**
     * @param string $contextPath
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\NotificationContext
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getOrCreateContextByPath($contextPath)
    {
        if(!array_key_exists($contextPath, $this->notificationContextByPathCache))
        {
            $notificationContext = $this->notificationContextRepository->findByPath($contextPath);
            if (!$notificationContext instanceof NotificationContext)
            {
                $notificationContext = new NotificationContext();
                $notificationContext->setPath($contextPath);
                $this->notificationContextRepository->createNotificationContext($notificationContext);
            }

            $this->notificationContextByPathCache[$contextPath] = $notificationContext;
        }

        return $this->notificationContextByPathCache[$contextPath];
    }

    /**
     * @param string $contextPath
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\NotificationContext
     */
    public function getContextByPath($contextPath)
    {
        if(!array_key_exists($contextPath, $this->notificationContextByPathCache))
        {
            $this->notificationContextByPathCache[$contextPath] = $this->notificationContextRepository->findByPath($contextPath);
            if(!$this->notificationContextByPathCache[$contextPath] instanceof NotificationContext)
            {
                throw new RuntimeException(sprintf('The given context %s could not be found', $contextPath));
            }
        }

        return $this->notificationContextByPathCache[$contextPath];
    }
}