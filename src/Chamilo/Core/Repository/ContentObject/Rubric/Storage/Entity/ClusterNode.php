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
class ClusterNode extends TreeNode
{
//    /**
//     * @var CategoryNode[] | ArrayCollection
//     *
//     * @ORM\OneToMany(targetEntity="CategoryNode", mappedBy="parentNode")
//     */
//    protected $categories;
//
//    /**
//     * @var CriteriumNode[] | ArrayCollection
//     *
//     * @ORM\OneToMany(targetEntity="CriteriumNode", mappedBy="parentNode")
//     */
//    protected $criteria;

//    /**
//     * ClusterNode constructor.
//     *
//     * @param string $title
//     * @param TreeNode|null $parentNode
//     */
//    public function __construct(string $title, TreeNode $parentNode = null)
//    {
//        parent::__construct($title, $parentNode);
//
////        $this->categories = new ArrayCollection();
////        $this->criteria = new ArrayCollection();
//    }

//    /**
//     * @return CategoryNode[]
//     */
//    public function getCategories(): ?array
//    {
//        return $this->categories;
//    }
//
//    /**
//     * @param CategoryNode[] $categories
//     *
//     * @return ClusterNode
//     */
//    public function setCategories(array $categories): ClusterNode
//    {
//        $this->categories = $categories;
//
//        return $this;
//    }
//
//    /**
//     * @return CriteriumNode[]
//     */
//    public function getCriteria(): ?array
//    {
//        return $this->criteria;
//    }
//
//    /**
//     * @param CriteriumNode[] $criteria
//     *
//     * @return ClusterNode
//     */
//    public function setCriteria(array $criteria): ClusterNode
//    {
//        $this->criteria = $criteria;
//
//        return $this;
//    }
//
//    /**
//     * @param CategoryNode $category
//     *
//     * @return self
//     */
//    public function addCategory(CategoryNode $category): self
//    {
//        $this->categories->add($category);
//        $this->children->add($category);
//
//        return $this;
//    }
//
//    /**
//     * @param CategoryNode $category
//     *
//     * @return self
//     */
//    public function removeCategory(CategoryNode $category): self
//    {
//        $this->categories->removeElement($category);
//        $this->children->removeElement($category);
//
//        return $this;
//    }
//
//    /**
//     * @param CriteriumNode $criterium
//     *
//     * @return self
//     */
//    public function addCriterium(CriteriumNode $criterium): self
//    {
//        $this->criteria->add($criterium);
//        $this->children->add($criterium);
//
//        return $this;
//    }
//
//    /**
//     * @param CriteriumNode $criterium
//     *
//     * @return self
//     */
//    public function removeCriterium(CriteriumNode $criterium): self
//    {
//        $this->criteria->removeElement($criterium);
//        $this->children->removeElement($criterium);
//
//        return $this;
//    }

}
