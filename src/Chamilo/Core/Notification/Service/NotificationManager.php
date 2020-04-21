<?php

namespace Chamilo\Core\Notification\Service;

use Chamilo\Core\Notification\Domain\FilterDTO;
use Chamilo\Core\Notification\Domain\NotificationDTO;
use Chamilo\Core\Notification\Domain\TranslationContext;
use Chamilo\Core\Notification\Storage\Entity\Filter;
use Chamilo\Core\Notification\Storage\Entity\Notification;
use Chamilo\Core\Notification\Storage\Entity\UserNotification;
use Chamilo\Core\Notification\Storage\Repository\NotificationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Faker\Provider\DateTime;
use InvalidArgumentException;
use RuntimeException;

/**
 * @package Chamilo\Core\Notification\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationManager
{
    /**
     * @var \Chamilo\Core\Notification\Storage\Repository\NotificationRepository
     */
    protected $notificationRepository;

    /**
     * @var NotificationTranslator
     */
    protected $notificationTranslator;

    /**
     * @var \Chamilo\Core\Notification\Service\NotificationContextManager
     */
    protected $contextManager;

    /**
     * NotificationManager constructor.
     *
     * @param \Chamilo\Core\Notification\Storage\Repository\NotificationRepository $notificationRepository
     * @param \Chamilo\Core\Notification\Service\NotificationContextManager $contextManager
     * @param NotificationTranslator $notificationTranslator
     */
    public function __construct(
        NotificationRepository $notificationRepository, NotificationContextManager $contextManager,
        NotificationTranslator $notificationTranslator
    )
    {
        $this->notificationRepository = $notificationRepository;
        $this->notificationTranslator = $notificationTranslator;
        $this->contextManager = $contextManager;
    }

    /**
     * @param string $url
     * @param \Chamilo\Core\Notification\Domain\ViewingContext[] $viewingContexts
     * @param \DateTime $date
     * @param array $targetUserIds
     * @param Filter[] $filters
     * @param string[] $contextPaths
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNotificationForUsers(
        $url, array $viewingContexts, $date, $targetUserIds = [], $filters = [], $contextPaths = []
    )
    {
        $notification = new Notification();

        foreach ($filters as $filter)
        {
            if (!$filter instanceof Filter)
            {
                throw new InvalidArgumentException(
                    sprintf('The given filters should be an instance of Filter, %s given', get_class($filter))
                );
            }
        }

        $notification->setUrl($url)
            ->setDescriptionContext(
                $this->notificationTranslator->createNotificationDescriptionContext($viewingContexts)
            )
            ->setDate($date)
            ->setFilters($filters);

        $this->notificationRepository->createNotification($notification);

        $userNotifications = [];

        foreach ($contextPaths as $contextPath)
        {
            $notificationContext = $this->contextManager->getOrCreateContextByPath($contextPath);

            foreach ($targetUserIds as $targetUserId)
            {
                $userNotification = new UserNotification();
                $userNotification->setNotification($notification)
                    ->setNotificationContext($notificationContext)
                    ->setUserId($targetUserId)
                    ->setRead(false)
                    ->setViewed(false)
                    ->setDate($date);

                $userNotifications[] = $userNotification;
            }
        }

        $this->notificationRepository->createUserNotifications($userNotifications);
    }

    /**
     * @param Notification[] $notifications
     * @param string $viewingContext
     *
     * @return \Chamilo\Core\Notification\Domain\NotificationDTO[]
     */
    public function formatNotifications($notifications, $viewingContext)
    {
        $notificationsData = [];

        foreach($notifications as $notification)
        {
            $filters = [];

            foreach($notification->getFilters() as $filter)
            {
                $filters[] = new FilterDTO(
                    $filter->getId(), $this->notificationTranslator->getTranslationFromFilter($filter)
                );
            }

            $notificationsData[] = new NotificationDTO(
                $notification->getId(),
                $this->notificationTranslator->getTranslationFromNotification($notification, $viewingContext),
                DatetimeUtilities::format_locale_date(null, $notification->getDate()->getTimestamp()),
                $notification->getUsers()[0]->isRead(),
                !$notification->getUsers()[0]->isViewed(),
                $filters
            );
        }

        return $notificationsData;
    }

    /**
     * @param int $notificationId
     *
     * @return Notification
     */
    public function getNotificationById($notificationId = 0)
    {
        if (empty($notificationId))
        {
            throw new RuntimeException('The given notification id can not be empty');
        }

        $notification = $this->notificationRepository->find($notificationId);
        if (!$notification instanceof Notification)
        {
            throw new RuntimeException(sprintf('The notification with id %s could not be found', $notificationId));
        }

        return $notification;
    }

    /**
     * @param string $contextPath
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $offset
     * @param int $count
     *
     * @return Notification[]
     */
    public function getNotificationsByContextPathForUser($contextPath, User $user, $offset = null, $count = null)
    {
        return $this->getNotificationsByContextPathsForUser([$contextPath], $user, $offset, $count);
    }

    /**
     * @param string[] $contextPaths
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $offset
     * @param int $count
     *
     * @return Notification[]
     */
    public function getNotificationsByContextPathsForUser(array $contextPaths, User $user, $offset = null, $count = null
    )
    {
        $contexts = [];

        foreach ($contextPaths as $contextPath)
        {
            try
            {
                $contexts[] = $this->contextManager->getContextByPath($contextPath);
            }
            catch(RuntimeException $ex)
            {

            }
        }

        if(empty($contexts))
        {
            return [];
        }

        $userNotifications =
            $this->notificationRepository->findUserNotificationsByContextsForUser($contexts, $user, $offset, $count);

        $notifications = [];

        foreach($userNotifications as $userNotification)
        {
            $notification = $userNotification->getNotification();
            $notification->setUsers([$userNotification]);
            $notifications[] = $notification;
        }

        return $notifications;
    }

    /**
     * @param string $contextPath
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countUnseenNotificationsByContextPathForUser($contextPath, User $user)
    {
        return $this->countUnseenNotificationsByContextPathsForUser([$contextPath], $user);
    }

    /**
     * @param string[] $contextPaths
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countUnseenNotificationsByContextPathsForUser(array $contextPaths, User $user)
    {
        $contexts = [];

        foreach ($contextPaths as $contextPath)
        {
            try
            {
                $contexts[] = $this->contextManager->getContextByPath($contextPath);
            }
            catch(RuntimeException $ex)
            {

            }
        }

        if(empty($contexts))
        {
            return 0;
        }

        return $this->notificationRepository->countUnseenNotificationsByContextsForUser($contexts, $user);
    }

    /**
     * @param string $contextPath
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setNotificationsViewedForUserAndContextPath($contextPath, User $user)
    {
        $this->setNotificationsViewedForUserAndContextPaths([$contextPath], $user);
    }

    /**
     * @param string[] $contextPaths
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setNotificationsViewedForUserAndContextPaths($contextPaths, User $user)
    {
        $contexts = [];

        foreach ($contextPaths as $contextPath)
        {
            try
            {
                $contexts[] = $this->contextManager->getContextByPath($contextPath);
            }
            catch(RuntimeException $ex)
            {

            }
        }

        if(empty($contextPaths))
        {
            return;
        }

        $this->notificationRepository->setNotificationsViewedForUserAndContexts($contexts, $user);
    }

    /**
     * @param Notification[] $notifications
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setNotificationsViewedForUser($notifications, User $user)
    {
        $this->notificationRepository->setNotificationsViewedForUser($notifications, $user);
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Notification $notification
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function setNotificationReadForUser(Notification $notification, User $user)
    {
        $this->notificationRepository->setNotificationReadForUser($notification, $user);
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Notification $notification
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function canUserViewNotification(Notification $notification, User $user)
    {
        return ($this->notificationRepository->countUserNotificationsByNotificationAndUser($notification, $user) > 0);
    }
}