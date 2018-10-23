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
 *          @ORM\Index(name="nun_read", columns={"is_read"}),
 *          @ORM\Index(name="nun_user_id", columns={"user_id"}),
 *          @ORM\Index(name="nun_user_read", columns={"user_id", "is_read"})
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

}