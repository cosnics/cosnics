<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity;

use Chamilo\Core\Repository\ContentObject\Rubric\Ajax\TreeNodeJSONModel;
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
    /**
     * @return array
     */
    public function getAllowedChildTypes()
    {
        return [CategoryNode::class, CriteriumNode::class];
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
        return new self($treeNodeJSONModel->getTitle(), $rubricData);
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
