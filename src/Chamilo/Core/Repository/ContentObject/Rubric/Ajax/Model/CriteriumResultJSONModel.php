<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model;

/**
 * Class RubricResultJSONModel
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model
 */
class CriteriumResultJSONModel
{
    /**
     * @var int
     */
    protected $criteriumTreeNodeId;

    /**
     * @var int
     */
    protected $choiceId;

    /**
     * CriteriumResultJSONModel constructor.
     *
     * @param int $criteriumTreeNodeId
     * @param int $choiceId
     */
    public function __construct(int $criteriumTreeNodeId, int $choiceId)
    {
        $this->criteriumTreeNodeId = $criteriumTreeNodeId;
        $this->choiceId = $choiceId;
    }

    /**
     * @return int
     */
    public function getCriteriumTreeNodeId(): ?int
    {
        return $this->criteriumTreeNodeId;
    }

    /**
     * @return int
     */
    public function getChoiceId(): ?int
    {
        return $this->choiceId;
    }
}
