<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Category
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
     * @var string
     */
    protected $color;

    /**
     * @var Criterium[] | ArrayCollection
     */
    protected $criteria;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->criteria = new ArrayCollection();
    }

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
     * @return Category
     */
    public function setId(int $id): Category
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
     * @return Category
     */
    public function setTitle(string $title): Category
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string $color
     *
     * @return Category
     */
    public function setColor(string $color): Category
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Criterium[]
     */
    public function getCriteria(): ?array
    {
        return $this->criteria;
    }

    /**
     * @param Criterium[] $criteria
     *
     * @return Category
     */
    public function setCriteria(array $criteria): Category
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @param Criterium $criterium
     *
     * @return Category
     */
    public function addCriterium(Criterium $criterium): Category
    {
        $this->criteria->add($criterium);

        return $this;
    }
}
