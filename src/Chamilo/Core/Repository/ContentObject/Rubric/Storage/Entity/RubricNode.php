<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity
 */
class RubricNode extends TreeNode
{
    /**
     * @var ClusterNode[]
     *
     * @ORM\OneToMany(targetEntity="ClusterNode", mappedBy="parentNode")
     */
    protected $clusters;

    /**
     * @var CategoryNode[]
     *
     * @ORM\OneToMany(targetEntity="ClusterNode", mappedBy="parentNode")
     */
    protected $categories;

    /**
     * @var CriteriumNode[]
     *
     * @ORM\OneToMany(targetEntity="ClusterNode", mappedBy="parentNode")
     */
    protected $criteria;

    /**
     * RubricData constructor.
     */
    public function __construct()
    {
        $this->clusters = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->criteria = new ArrayCollection();
    }

    /**
     * @return ClusterNode[]
     */
    public function getClusters(): ?array
    {
        return $this->clusters;
    }

    /**
     * @param ClusterNode[] $clusters
     *
     * @return RubricNode
     */
    public function setClusters(array $clusters): RubricNode
    {
        $this->clusters = $clusters;

        return $this;
    }

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
     * @return RubricNode
     */
    public function setCategories(array $categories): RubricNode
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
     * @return RubricNode
     */
    public function setCriteria(array $criteria): RubricNode
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @param ClusterNode $cluster
     *
     * @return RubricNode
     */
    public function addCluster(ClusterNode $cluster): RubricNode
    {
        $this->clusters->add($cluster);
        $this->children->add($cluster);

        return $this;
    }

    /**
     * @param ClusterNode $cluster
     *
     * @return RubricNode
     */
    public function removeCluster(ClusterNode $cluster): RubricNode
    {
        $this->clusters->removeElement($cluster);
        $this->children->removeElement($cluster);

        return $this;
    }

    /**
     * @param CategoryNode $category
     *
     * @return RubricNode
     */
    public function addCategory(CategoryNode $category): RubricNode
    {
        $this->categories->add($category);
        $this->children->add($category);

        return $this;
    }

    /**
     * @param CategoryNode $category
     *
     * @return RubricNode
     */
    public function removeCategory(CategoryNode $category): RubricNode
    {
        $this->categories->removeElement($category);
        $this->children->removeElement($category);

        return $this;
    }

    /**
     * @param CriteriumNode $criterium
     *
     * @return RubricNode
     */
    public function addCriterium(CriteriumNode $criterium): RubricNode
    {
        $this->criteria->add($criterium);
        $this->children->add($criterium);

        return $this;
    }

    /**
     * @param CriteriumNode $criterium
     *
     * @return RubricNode
     */
    public function removeCriterium(CriteriumNode $criterium): RubricNode
    {
        $this->criteria->removeElement($criterium);
        $this->children->removeElement($criterium);

        return $this;
    }

}
