<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Cluster
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
     * @var Category[]
     */
    protected $categories;

    /**
     * @var Criterium[]
     */
    protected $criteria;

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
     * @return Cluster
     */
    public function setId(int $id): Cluster
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
     * @return Cluster
     */
    public function setTitle(string $title): Cluster
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Category[]
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }

    /**
     * @param Category[] $categories
     *
     * @return Cluster
     */
    public function setCategories(array $categories): Cluster
    {
        $this->categories = $categories;

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
     * @return Cluster
     */
    public function setCriteria(array $criteria): Cluster
    {
        $this->criteria = $criteria;

        return $this;
    }

}
