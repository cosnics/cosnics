<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model;

use JMS\Serializer\Annotation\Type;

/**
 * Class RubricResultJSONModel
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model
 */
class CriteriumResultJSONModel
{
    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $criteriumTreeNodeId;

    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $choiceId;

    /**
     * @var string
     *
     * @Type("string")
     */
    protected $comment;

    /**
     * CriteriumResultJSONModel constructor.
     *
     * @param int $criteriumTreeNodeId
     * @param int $choiceId
     * @param string|null $comment
     */
    public function __construct(int $criteriumTreeNodeId, int $choiceId, string $comment = null)
    {
        $this->criteriumTreeNodeId = $criteriumTreeNodeId;
        $this->choiceId = $choiceId;
        $this->comment = $comment;
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

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }
}
