<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Criterium
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $weight;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Criterium
     */
    public function setId(int $id): Criterium
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Criterium
     */
    public function setTitle(string $title): Criterium
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight(): ?int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     *
     * @return Criterium
     */
    public function setWeight(int $weight): Criterium
    {
        $this->weight = $weight;

        return $this;
    }


}
