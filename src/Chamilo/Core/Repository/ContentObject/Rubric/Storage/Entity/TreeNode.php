<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity
 *
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @DiscriminatorMap({
 *     "rubric" = "RubricNode", "cluster" = "ClusterNode", "category" = "CategoryNode", "criterium" = "CriteriumNode"
 * })
 *
 * @ORM\Table(
 *      name="repository_rubric_tree_node"
 * )
 */
class TreeNode
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
     * @ManyToOne(targetEntity="TreeNode")
     * @JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parentNode;

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
}
