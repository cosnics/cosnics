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
     * @return int
     */
    public function getId(): int
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
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
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
     */
    public function setNotification(Notification $notification)
    {
        $this->notification = $notification;
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
     */
    public function setRead(bool $read)
    {
        $this->read = $read;
    }

}