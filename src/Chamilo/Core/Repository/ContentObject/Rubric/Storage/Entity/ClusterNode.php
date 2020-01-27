<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity
 */
class ClusterNode extends TreeNode
{
    /**
     * @var CategoryNode[]
     *
     * @OneToMany(targetEntity="CategoryNode", mappedBy="parentNode")
     */
    protected $categories;

    /**
     * @var CriteriumNode[]
     *
     * @OneToMany(targetEntity="CriteriumNode", mappedBy="parentNode")
     */
    protected $criteria;

    /**
     * @return CategoryNode[]
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }

    /**
     * @param CategoryNode[] $categories
     *
     * @return ClusterNode
     */
    public function setCategories(array $categories): ClusterNode
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return CriteriumNode[]
     */
    public function getCriteria(): ?array
    {
        return $this->criteria;
    }

    /**
     * @param CriteriumNode[] $criteria
     *
     * @return ClusterNode
     */
    public function setCriteria(array $criteria): ClusterNode
    {
        $this->criteria = $criteria;

        return $this;
    }

}
