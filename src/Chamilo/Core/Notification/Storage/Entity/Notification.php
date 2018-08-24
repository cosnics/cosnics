<?php

namespace Chamilo\Core\Notification\Storage\Entity;

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
     * @ORM\Column(name="description_variable", type="string", nullable=false)
     */
    protected $descriptionVariable;

    /**
     * @var string
     *
     * @ORM\Column(name="description_parameters", type="string", nullable=false)
     */
    protected $descriptionParameters;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescriptionVariable()
    {
        return $this->descriptionVariable;
    }

    /**
     * @param string $descriptionVariable
     */
    public function setDescriptionVariable(string $descriptionVariable)
    {
        $this->descriptionVariable = $descriptionVariable;
    }

    /**
     * @return string
     */
    public function getDescriptionParameters()
    {
        return $this->descriptionParameters;
    }

    /**
     * @param string $descriptionParameters
     */
    public function setDescriptionParameters(string $descriptionParameters)
    {
        $this->descriptionParameters = $descriptionParameters;
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
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
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
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
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
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param \Chamilo\Core\Notification\Storage\Entity\Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }
}