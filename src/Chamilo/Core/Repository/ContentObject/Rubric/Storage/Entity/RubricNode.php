<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model\TreeNodeJSONModel;
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
     * @return array
     */
    public function getAllowedChildTypes()
    {
        return [ClusterNode::class, CategoryNode::class, CriteriumNode::class];
    }

    /**
     * @param TreeNodeJSONModel $treeNodeJSONModel
     * @param RubricData $rubricData
     *
     * @return RubricNode
     * @throws \Chamilo\Core\Repository\ContentObject\Rubric\Domain\Exceptions\InvalidChildTypeException
     */
    public static function fromJSONModel(TreeNodeJSONModel $treeNodeJSONModel, RubricData $rubricData): TreeNode
    {
        $node = new self($treeNodeJSONModel->getTitle(), $rubricData);
        $node->updateFromJSONModel($treeNodeJSONModel);

        return $node;
    }

    /**
     * @param TreeNodeJSONModel $treeNodeJSONModel
     *
     * @return TreeNode
     */
    public function updateFromJSONModel(TreeNodeJSONModel $treeNodeJSONModel): TreeNode
    {
        if ($treeNodeJSONModel->getType() != TreeNodeJSONModel::TYPE_RUBRIC)
        {
            throw new \InvalidArgumentException('The TreeNodeJSONModel does not have the correct type');
        }

        parent::updateFromJSONModel($treeNodeJSONModel);

        return $this;
    }

    /**
     * @return TreeNodeJSONModel
     * @throws \Exception
     */
    public function toJSONModel(): TreeNodeJSONModel
    {
        return new TreeNodeJSONModel(
            $this->getId(), $this->getTitle(), TreeNodeJSONModel::TYPE_RUBRIC, $this->getParentNodeId()
        );
    }
}
