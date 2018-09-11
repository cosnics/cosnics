<?php

namespace Chamilo\Core\Notification\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Notification\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NotificationTrigger
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
     * @var string
     *
     * @ORM\Column(name="description_variable", type="string", nullable=false, length="1024")
     */
    protected $notificationContext;

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
     * @return string
     */
    public function getNotificationContext()
    {
        return $this->notificationContext;
    }

    /**
     * @param string $notificationContext
     *
     * @return NotificationTrigger
     */
    public function setNotificationContext(string $notificationContext)
    {
        $this->notificationContext = $notificationContext;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return NotificationTrigger
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }
}