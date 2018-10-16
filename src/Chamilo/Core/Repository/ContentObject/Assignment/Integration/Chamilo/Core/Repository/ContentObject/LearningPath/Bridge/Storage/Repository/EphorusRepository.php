<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Storage\Repository\AssignmentEphorusRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EphorusRepository extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository\EphorusRepository
{
    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject[]|\Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findAssignmentEntriesWithRequestsByTreeNodeData(
        TreeNodeData $treeNodeData, RecordRetrievesParameters $recordRetrievesParameters = null
    )
    {
        $entryConditions =
            $this->getConditionForTreeNodeData($treeNodeData, $recordRetrievesParameters->getCondition());
        $recordRetrievesParameters->setCondition($entryConditions);

        return $this->findAssignmentEntriesWithRequests($recordRetrievesParameters);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int[] $entryIds
     *
     * @return Request[]
     */
    public function findEphorusRequestsForAssignmentEntriesByTreeNodeData(
        TreeNodeData $treeNodeData, array $entryIds = []
    )
    {
        return $this->findEphorusRequestsForAssignmentEntries(
            $entryIds, $this->getConditionForTreeNodeData($treeNodeData)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithRequestsByTreeNodeData(
        TreeNodeData $treeNodeData, Condition $condition = null
    )
    {
        return $this->countAssignmentEntriesWithRequests($this->getConditionForTreeNodeData($treeNodeData, $condition));
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return Condition
     */
    private function getConditionForTreeNodeData(TreeNodeData $treeNodeData, Condition $condition = null)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Entry::class, Entry::PROPERTY_TREE_NODE_DATA_ID),
            new StaticConditionVariable($treeNodeData->getId())
        );

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        return new AndCondition($conditions);
    }
}