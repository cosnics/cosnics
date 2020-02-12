<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\TreeNodeJSONModel;
use Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException;
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
     * @ORM\Column(name="depth", type="integer")
     */
    protected $depth = 0;

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
     *
     * @throws InvalidChildTypeException
     */
    public function __construct(string $title, RubricData $rubricData, TreeNode $parentNode = null)
    {
        $this->title = $title;

        $this->setRubricData($rubricData);
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
     * @throws InvalidChildTypeException
     */
    public function setRubricData(RubricData $rubricData = null): self
    {
        if ($this->rubricData === $rubricData)
        {
            return $this;
        }

        $oldRubricData = $this->rubricData;
        $this->rubricData = $rubricData;

        if ($oldRubricData instanceof RubricData)
        {
            $oldRubricData->removeTreeNode($this);
        }

        if($this->getParentNode())
        {
            $this->setParentNode(null);
        }

        if ($rubricData instanceof RubricData)
        {
            $rubricData->addTreeNode($this);
        }

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
     * @return int|null
     */
    public function getParentNodeId(): ?int
    {
        return $this->parentNode instanceof TreeNode ? $this->parentNode->getId() : null;
    }

    /**
     * @param TreeNode|null $newParentNode
     *
     * @return TreeNode
     * @throws InvalidChildTypeException
     */
    public function setParentNode(TreeNode $newParentNode = null): TreeNode
    {
        if ($this->getRubricData() && $this === $this->getRubricData()->getRootNode())
        {
            throw new \InvalidArgumentException('You can not change the parent node of the root node');
        }

        if ($this->parentNode === $newParentNode)
        {
            return $this;
        }

        $oldParentNode = $this->parentNode;
        $this->parentNode = $newParentNode;

        if ($oldParentNode instanceof TreeNode)
        {
            $oldParentNode->removeChild($this);
        }

        if ($newParentNode instanceof TreeNode)
        {
            $newParentNode->addChild($this);
        }

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
     * @return int
     */
    public function getDepth(): ?int
    {
        return $this->depth;
    }

    /**
     * WARNING: NEVER EVER EVER change the depth of this class manually. This method should only be used
     * by referenced methods and is used to set the correct sorting order to retrieve the tree nodes from the database.
     * Changing the depth will in no way move this item to another parent. The validator will make sure that the depths
     * of the nodes are respected.
     *
     * @param int $depth
     *
     * @return TreeNode
     */
    public function setDepth(int $depth): TreeNode
    {
        $parentNode = $this->getParentNode();

        if ($parentNode instanceof TreeNode && ($depth - $parentNode->getDepth() != 1))
        {
            throw new \InvalidArgumentException(
                sprintf(
                    'The given child depth was expected to be %s but was given %s',
                    $parentNode->getDepth() + 1, $depth
                )
            );
        }

        $this->depth = $depth;

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
     * @throws InvalidChildTypeException
     */
    public function setChildren(ArrayCollection $children): self
    {
        $this->children = $children;

        foreach ($this->children as $child)
        {
            if (!$this->isChildTypeValid($child))
            {
                throw new InvalidChildTypeException($this, $child);
            }

            $child->setParentNode($this);
        }

        return $this;
    }

    /**
     * @param TreeNode $childToAdd
     *
     * @return $this
     * @throws InvalidChildTypeException
     */
    public function addChild(TreeNode $childToAdd): self
    {
        if ($this->hasChild($childToAdd))
        {
            return $this;
        }

        if (!$this->isChildTypeValid($childToAdd))
        {
            throw new InvalidChildTypeException($this, $childToAdd);
        }

        $this->children->add($childToAdd);
        $childToAdd->setParentNode($this);
        $childToAdd->setSort($this->children->count());
        $childToAdd->setDepth($this->getDepth() + 1);

        return $this;
    }

    /**
     * @param TreeNode $child
     *
     * @return bool
     */
    public function isChildTypeValid(TreeNode $child)
    {
        return in_array(get_class($child), $this->getAllowedChildTypes());
    }

    /**
     * @param TreeNode $childToAdd
     * @param int $sortValue
     *
     * @return TreeNode
     * @throws InvalidChildTypeException
     */
    public function insertChild(TreeNode $childToAdd, int $sortValue): self
    {
        if ($this->hasChild($childToAdd))
        {
            return $this;
        }

        $this->addChild($childToAdd);
        $childToAdd->setSort($sortValue);

        foreach ($this->children as $child)
        {
            if ($child === $childToAdd)
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
     *
     * @noinspection PhpUnhandledExceptionInspection
     * @throws InvalidChildTypeException
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
     *
     * @throws \InvalidArgumentException
     * @throws InvalidChildTypeException
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function moveChild(TreeNode $childNode, int $newPosition)
    {
        if (!$this->hasChild($childNode))
        {
            throw new \InvalidArgumentException(
                sprintf('The given tree node %s is not a child of tree node %s', $childNode->getId(), $this->getId())
            );
        }

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

    abstract function getAllowedChildTypes();

    /**
     * @param TreeNodeJSONModel $treeNodeJSONModel
     * @param RubricData $rubricData
     *
     * @return TreeNode
     */
    abstract public static function fromJSONModel(
        TreeNodeJSONModel $treeNodeJSONModel, RubricData $rubricData
    ): TreeNode;

    /**
     * @return TreeNodeJSONModel
     */
    abstract public function toJSONModel(): TreeNodeJSONModel;

    /**
     * @param TreeNodeJSONModel $treeNodeJSONModel
     *
     * @return TreeNode
     */
    public function updateFromJSONModel(TreeNodeJSONModel $treeNodeJSONModel): TreeNode
    {
        $this->setTitle($treeNodeJSONModel->getTitle());
        return $this;
    }
}
