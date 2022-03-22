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
     * @var ?float
     */
    protected $score;

    /**
     * TreeNodeResultJSONModel constructor.
     *
     * @param int $treeNodeId
     * @param float|null $score
     * @param int|null $levelId
     * @param string|null $comment
     */
    public function __construct(int $treeNodeId, ?float $score, int $levelId = null, string $comment = null)
    {
        $this->treeNodeId = $treeNodeId;
        $this->levelId = $levelId;
        $this->comment = $comment;
        $this->score = $score;
    }
}
