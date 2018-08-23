<?php

namespace Chamilo\Core\Notification\Storage\Entity;

/**
 * @package Chamilo\Core\Notification\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Notification
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $descriptionVariable;

    /**
     * @var string
     */
    protected $descriptionParameters;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var \Chamilo\Core\Notification\Storage\Entity\Filter[]
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


}