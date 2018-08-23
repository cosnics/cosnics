<?php

namespace Chamilo\Core\Notification\Storage\Entity;

class Filter
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $descriptionVariable;

    /**
     * @var string
     */
    protected $descriptionParameters;

    /**
     * @var \Chamilo\Core\Notification\Storage\Entity\Notification[]
     */
    protected $notifications;

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
     */
    public function setPath(string $path)
    {
        $this->path = $path;
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


}