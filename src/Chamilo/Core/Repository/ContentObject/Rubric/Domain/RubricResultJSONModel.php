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
     * @param RubricUserJSONModel $user
     * @param \DateTime $date
     * @param TreeNodeResultJSONModel[] $results
     */
    public function __construct(
        RubricUserJSONModel $user, \DateTime $date, array $results = []
    )
    {
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

}
