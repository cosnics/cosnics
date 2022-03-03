<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Domain;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;

/**
 * Class RubricResultScore
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Domain
 *
 * @author - Stefan Gabriëls - Hogeschool Gent
 */
class RubricTreeNodeScore
{
    /**
     * @var TreeNode
     */
    private $treeNode;

    /**
     * @var float
     */
    private float $score;

    /**
     * @var ?float
     */
    private ?float $weight;

    /**
     * @param TreeNode $treeNode
     * @param float $score
     * @param float|null $weight
     */
    public function __construct(TreeNode $treeNode, float $score, ?float $weight = null)
    {
        $this->treeNode = $treeNode;
        $this->score = $score;
        $this->weight = $weight;
    }

    /**
     * @return TreeNode
     */
    public function getTreeNode(): TreeNode
    {
        return $this->treeNode;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @return float|null
     */
    public function getWeight(): ?float
    {
        return $this->weight;
    }
}
