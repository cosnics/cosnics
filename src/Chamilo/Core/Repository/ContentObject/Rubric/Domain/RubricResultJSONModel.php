<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Domain;

/**
 * Class RubricResultJSONModel
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Domain
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class RubricResultJSONModel
{
    /**
     * @var string
     */
    protected $resultId;

    /**
     * @var RubricUserJSONModel
     */
    protected $user;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var TreeNodeResultJSONModel[]
     */
    protected $results;

    /**
     * RubricResultJSONModel constructor.
     *
     * @param string $resultId
     * @param RubricUserJSONModel $user
     * @param \DateTime $date
     * @param TreeNodeResultJSONModel[] $results
     */
    public function __construct(
        string $resultId, RubricUserJSONModel $user, \DateTime $date, array $results = []
    )
    {
        $this->resultId = $resultId;
        $this->user = $user;
        $this->date = $date;
        $this->results = $results;
    }

    /**
     * @param TreeNodeResultJSONModel $treeNodeResultJSONModel
     */
    public function addTreeNodeResult(TreeNodeResultJSONModel $treeNodeResultJSONModel)
    {
        $this->results[] = $treeNodeResultJSONModel;
    }

    /**
     * @return string
     */
    public function getResultId(): string
    {
        return $this->resultId;
    }

}
