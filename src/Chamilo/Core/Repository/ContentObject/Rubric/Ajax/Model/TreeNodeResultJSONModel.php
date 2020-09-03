<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model;

use JMS\Serializer\Annotation\Type;

/**
 * Class RubricResultJSONModel
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model
 */
class TreeNodeResultJSONModel
{
    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $treeNodeId;

    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $levelId;

    /**
     * @var string
     *
     * @Type("string")
     */
    protected $comment;

    /**
     * CriteriumResultJSONModel constructor.
     *
     * @param int $treeNodeId
     * @param int $levelId
     * @param string|null $comment
     */
    public function __construct(int $treeNodeId, int $levelId, string $comment = null)
    {
        $this->treeNodeId = $treeNodeId;
        $this->levelId = $levelId;
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getTreeNodeId(): ?int
    {
        return $this->treeNodeId;
    }

    /**
     * @return int
     */
    public function getLevelId(): ?int
    {
        return $this->levelId;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }
}
