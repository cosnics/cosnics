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
class CategoryNode extends TreeNode
{
    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255, nullable=true)
     */
    protected $color;

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string $color
     *
     * @return CategoryNode
     */
    public function setColor(string $color): CategoryNode
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedChildTypes()
    {
        return [CriteriumNode::class];
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
     * @return TreeNodeJSONModel
     * @throws \Exception
     */
    public function toJSONModel(): TreeNodeJSONModel
    {
        return new TreeNodeJSONModel(
            $this->getId(), $this->getTitle(), TreeNodeJSONModel::TYPE_CATEGORY, $this->getParentNodeId(),
            $this->getColor()
        );
    }

    /**
     * @param TreeNodeJSONModel $treeNodeJSONModel
     *
     * @return TreeNode
     */
    public function updateFromJSONModel(TreeNodeJSONModel $treeNodeJSONModel): TreeNode
    {
        if ($treeNodeJSONModel->getType() != TreeNodeJSONModel::TYPE_CATEGORY)
        {
            throw new \InvalidArgumentException('The TreeNodeJSONModel does not have the correct type');
        }

        parent::updateFromJSONModel($treeNodeJSONModel);
        $this->setColor($treeNodeJSONModel->getColor());

        return $this;
    }
}
