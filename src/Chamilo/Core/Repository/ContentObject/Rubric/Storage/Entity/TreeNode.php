<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity(repositoryClass="Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository\TreeNodeRepository")
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
     * @ORM\OneToMany(targetEntity="TreeNode", mappedBy="parentNode", cascade={"persist", "refresh"})
     * @ORM\OrderBy({"sort" = "asc"})
     */
    protected $children;

    /**
     * TreeNode constructor.
     *
     * @param string $title
     * @param RubricData $rubricData
     * @param TreeNode $parentNode
     */
    public function __construct(string $title, RubricData $rubricData, TreeNode $parentNode = null)
    {
        $this->title = $title;
        $this->rubricData = $rubricData;

        $rubricData->addTreeNode($this);
        $this->setParentNode($parentNode);

        $this->children = new ArrayCollection();
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
     * @return TreeNode
     */
    public function getParentNode(): ?TreeNode
    {
        return $this->parentNode;
    }

    /**
     * @param TreeNode|null $newParentNode
     *
     * @return TreeNode
     */
    public function setParentNode(TreeNode $newParentNode = null): TreeNode
    {
        if ($this === $this->getRubricData()->getRootNode())
        {
            throw new \InvalidArgumentException('You can not change the parent node of the root node');
        }

        if ($this->parentNode === $newParentNode)
        {
            return $this;
        }

        if ($this->hasParentNode())
        {
            $this->parentNode->removeChild($this);
        }

        if ($newParentNode instanceof TreeNode)
        {
            $newParentNode->addChild($this);
        }

        $this->parentNode = $newParentNode;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasParentNode(): bool
    {
        return $this->parentNode instanceof TreeNode;
    }

    /**
     * @return int
     */
    public function getSort(): ?int
    {
        return $this->sort;
    }

    /**
     * This method should only be called by other methods e.g. the parent treenode that changes the sort of his
     * children.
     *
     * WARNING: due to domain limitations this method will not change the sort of the sibling treenodes. Calling
     * this method directly to "move" this node to a different location will result in a broken domain model.
     * To prevent storing a false domain model in the database the entire structure of the rubric will be validated
     * upon storing it into the database. Making sure that any changes in the sort that are invalid
     * (same sort value multiple times, sort value out of range, gaps between sort values)
     * will not be stored into the database.
     *
     * @param int $sort
     *
     * @return TreeNode
     */
    public function setSort(int $sort): self
    {
        $parentNode = $this->getParentNode();

        if ($parentNode instanceof TreeNode && !$parentNode->isChildSortValid($sort))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given child sort must be between 1 and %s, %s given',
                    $parentNode->getChildren()->count(), $sort
                )
            );
        }

        $this->sort = $sort;

        return $this;
    }

    /**
     * @param int $sort
     *
     * @return bool
     */
    public function isChildSortValid(int $sort)
    {
        return $sort >= 1 && $sort <= $this->getChildren()->count();
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
     * @param TreeNode $childToAdd
     *
     * @return $this
     */
    public function addChild(TreeNode $childToAdd): self
    {
        if ($this->hasChild($childToAdd))
        {
            return $this;
        }

        $this->children->add($childToAdd);
        $childToAdd->setParentNode($this);
        $childToAdd->setSort($this->children->count());

        return $this;
    }

    /**
     * @param TreeNode $childToAdd
     * @param int $sortValue
     *
     * @return TreeNode
     */
    public function insertChild(TreeNode $childToAdd, int $sortValue): self
    {
        if ($this->hasChild($childToAdd))
        {
            return $this;
        }

        $this->children->add($childToAdd);

        $childToAdd->setParentNode($this);
        $childToAdd->setSort($sortValue);

        foreach ($this->children as $child)
        {
            if($child === $childToAdd)
            {
                continue;
            }

            if ($child->getSort() >= $sortValue)
            {
                $child->setSort($child->getSort() + 1);
            }
        }

        return $this;
    }

    /**
     * @param TreeNode $childToRemove
     *
     * @return $this
     */
    public function removeChild(TreeNode $childToRemove): self
    {
        if (!$this->hasChild($childToRemove))
        {
            return $this;
        }

        foreach ($this->children as $child)
        {
            if ($child == $childToRemove)
            {
                continue;
            }

            if ($child->getSort() > $childToRemove->getSort())
            {
                $child->setSort($child->getSort() - 1);
            }
        }

        $this->children->removeElement($childToRemove);
        $childToRemove->setParentNode(null);

        return $this;
    }

    /**
     * @param TreeNode $childNode
     * @param int $newPosition
     */
    public function moveChild(TreeNode $childNode, int $newPosition)
    {
        $this->removeChild($childNode);
        $this->insertChild($childNode, $newPosition);
    }

    /**
     * @param TreeNode $possibleChildNode
     *
     * @return bool
     */
    public function hasChild(TreeNode $possibleChildNode)
    {
        return $this->children->contains($possibleChildNode);
    }
}
