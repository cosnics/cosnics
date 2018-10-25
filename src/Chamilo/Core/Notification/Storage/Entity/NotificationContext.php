<?php

namespace Chamilo\Core\Notification\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Notification\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity(repositoryClass="Chamilo\Core\Notification\Storage\Repository\NotificationContextRepository")
 * @ORM\Table(
 *     name="notification_context",
 *     indexes={@ORM\Index(name="nc_path", columns={"path"})}
 * )
 */
class NotificationContext
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
     * @ORM\Column(name="path", type="string", nullable=false, length=512)
     */
    protected $path;

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
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\NotificationContext
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }
}