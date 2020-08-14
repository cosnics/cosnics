<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Domain;

/**
 * Class TreeNodeResultJSONModel
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Domain
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeResultJSONModel
{
    /**
     * @var int
     */
    protected $treeNodeId;

    /**
     * @var int
     */
    protected $levelId;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var float
     */
    protected $score;

    /**
     * TreeNodeResultJSONModel constructor.
     *
     * @param int $treeNodeId
     * @param int $levelId
     * @param string $comment
     * @param float $score
     */
    public function __construct(int $treeNodeId, int $levelId, string $comment, float $score)
    {
        $this->treeNodeId = $treeNodeId;
        $this->levelId = $levelId;
        $this->comment = $comment;
        $this->score = $score;
    }
}
