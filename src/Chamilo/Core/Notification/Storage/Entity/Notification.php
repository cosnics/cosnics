<?php
namespace Chamilo\Core\Notification\Storage\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Notification\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity(repositoryClass="Chamilo\Core\Notification\Storage\Repository\NotificationRepository")
 * @ORM\Table(
 *     name="notification_notification",
 *     indexes={@ORM\Index(name="nn_date", columns={"date"})}
 * )
 */
class Notification
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
     * @ORM\Column(name="description_context", type="text", nullable=false)
     */
    protected $descriptionContext;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", nullable=false)
     */
    protected $url;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    protected $date;

    /**
     * @var \Chamilo\Core\Notification\Storage\Entity\Filter[]
     *
     * @ORM\ManyToMany(targetEntity="Chamilo\Core\Notification\Storage\Entity\Filter", inversedBy="notifications")
     * @ORM\JoinTable(name="notification_filter_relation",
     *      joinColumns={@ORM\JoinColumn(name="notification_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="filter_id", referencedColumnName="id")}
     *      )
     */
    protected $filters;

    /**
     * @var \Chamilo\Core\Notification\Storage\Entity\UserNotification[]
     * @ORM\OneToMany(targetEntity="Chamilo\Core\Notification\Storage\Entity\UserNotification", mappedBy="notification")
     */
    protected $users;

    /**
     * Notification constructor.
     */
    public function __construct()
    {
        $this->filters = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

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
    public function getDescriptionContext()
    {
        return $this->descriptionContext;
    }

    /**
     * @param string $descriptionContext
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\Notification
     */
    public function setDescriptionContext(string $descriptionContext)
    {
        $this->descriptionContext = $descriptionContext;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\Notification
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

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
     * @return \Chamilo\Core\Notification\Storage\Entity\Notification
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return \Chamilo\Core\Notification\Storage\Entity\Filter[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Filter[] $filters
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\Notification
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Filter $filter
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\Notification
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * @return \Chamilo\Core\Notification\Storage\Entity\UserNotification[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\UserNotification[] $users
     *
     * @return \Chamilo\Core\Notification\Storage\Entity\Notification
     */
    public function setUsers(array $users)
    {
        $this->users = $users;

        return $this;
    }
}