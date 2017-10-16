<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Exception;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeNotFoundException extends \Exception
{
    /**
     * @var int
     */
    protected $treeNodeDataId;

    /**
     * TreeNodeNotFoundException constructor.
     *
     * @param int $treeNodeDataId
     */
    public function __construct($treeNodeDataId)
    {
        $this->treeNodeDataId = $treeNodeDataId;
    }

    /**
     * @return int
     */
    public function getTreeNodeDataId(): int
    {
        return $this->treeNodeDataId;
    }
}