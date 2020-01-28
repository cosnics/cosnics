<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "rubric" = "RubricNode", "cluster" = "ClusterNode", "category" = "CategoryNode", "criterium" = "CriteriumNode"
 * })
 *
 * @ORM\Table(
 *      name="repository_rubric_tree_node"
 * )
 */
abstract class TreeNode
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var TreeNode
     *
     * @ORM\ManyToOne(targetEntity="TreeNode")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parentNode;

    /**
     * @var RubricData
     *
     * @ORM\ManyToOne(targetEntity="RubricData")
     * @ORM\JoinColumn(name="rubric_data_id", referencedColumnName="id")
     */
    protected $rubricData;

    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer")
     */
    protected $sort;

    /**
     * @var TreeNode[] | ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="TreeNode", mappedBy="parentNode")
     */
    protected $children;

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
     * @return TreeNode
     */
    public function setId(int $id): TreeNode
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
     * @return TreeNode
     */
    public function setTitle(string $title): TreeNode
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return TreeNode
     */
    public function getParentNode(): ?TreeNode
    {
        return $this->parentNode;
    }

    /**
     * @param TreeNode $parentNode
     *
     * @return TreeNode
     */
    public function setParentNode(TreeNode $parentNode): TreeNode
    {
        $this->parentNode = $parentNode;

        return $this;
    }

    /**
     * @return RubricData
     */
    public function getRubricData(): ?RubricData
    {
        return $this->rubricData;
    }

    /**
     * @param RubricData $rubricData
     *
     * @return TreeNode
     */
    public function setRubricData(RubricData $rubricData): self
    {
        $this->rubricData = $rubricData;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort(): ?int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     *
     * @return TreeNode
     */
    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return TreeNode[]|ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param TreeNode[]|ArrayCollection $children
     *
     * @return TreeNode
     */
    public function setChildren($children): self
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @param TreeNode $child
     *
     * @return $this
     */
    public function addChild(TreeNode $child): self
    {
        $this->children->add($child);

        return $this;
    }

    /**
     * @param TreeNode $child
     *
     * @return $this
     */
    public function removeChild(TreeNode $child): self
    {
        $this->children->removeElement($child);

        return $this;
    }
}
