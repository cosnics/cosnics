<?php

namespace Chamilo\Core\Notification\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Notification\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="notification_user_notification",
 *     indexes={
 *          @ORM\Index(name="nun_user_id", columns={"user_id"}),
 *          @ORM\Index(name="nun_user_notification", columns={"user_id", "notification_id"}),
 *          @ORM\Index(name="nun_user_notification_viewed", columns={"user_id", "notification_context", "is_viewed"})
 *     }
 * )
 */
class UserNotification
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    protected $userId;

    /**
     * @var Notification
     *
     * @ORM\ManyToOne(targetEntity="Chamilo\Core\Notification\Storage\Entity\Notification")
     * @ORM\JoinColumn(name="notification_id", nullable=false)
     */
    protected $notification;

    /**
     * @var NotificationContext
     *
     * @ORM\ManyToOne(targetEntity="Chamilo\Core\Notification\Storage\Entity\NotificationContext")
     * @ORM\JoinColumn(name="notification_context", nullable=false)
     */
    protected $notificationContext;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_read", type="boolean")
     */
    protected $read;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_viewed", type="boolean")
     */
    protected $viewed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    protected $date;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\UserNotification
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param Notification $notification
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\UserNotification
     */
    public function setNotification(Notification $notification)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRead()
    {
        return $this->read;
    }

    /**
     * @param bool $read
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\UserNotification
     */
    public function setRead(bool $read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * @return bool
     */
    public function isViewed()
    {
        return $this->viewed;
    }

    /**
     * @param bool $viewed
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\UserNotification
     */
    public function setViewed(bool $viewed)
    {
        $this->viewed = $viewed;

        return $this;
    }

    /**
     * @return NotificationContext
     */
    public function getNotificationContext(): NotificationContext
    {
        return $this->notificationContext;
    }

    /**
     * @param NotificationContext $notificationContext
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\UserNotification
     */
    public function setNotificationContext(NotificationContext $notificationContext)
    {
        $this->notificationContext = $notificationContext;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\UserNotification
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

}
