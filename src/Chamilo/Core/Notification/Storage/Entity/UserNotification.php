<?php
namespace Chamilo\Core\Notification\Storage\Entity;

use DateTime;

/**
 * @package Chamilo\Core\Notification\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="notification_user_notification",
 *     indexes={
 *          @ORM\Index(name="nun_read", columns={"is_read"}),
 *          @ORM\Index(name="nun_viewed", columns={"is_viewed"}),
 *          @ORM\Index(name="nun_user_id", columns={"user_id"}),
 *          @ORM\Index(name="nun_date", columns={"date"}),
 *          @ORM\Index(name="nun_user_read", columns={"user_id", "is_read"})
 *     }
 * )
 */
class UserNotification
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    protected $date;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    protected $id;

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
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    protected $userId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_viewed", type="boolean")
     */
    protected $viewed;

    /**
     * @return \DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\UserNotification
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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

}