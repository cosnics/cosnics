<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
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
class LearningPathAssignmentEphorusRepository extends
    \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathAssignmentEphorusRepository
{
    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject[]|\Chamilo\Libraries\Storage\Iterator\DataClassCollection
     */
    public function findAssignmentEntriesWithRequestsByTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData,
        RecordRetrievesParameters $recordRetrievesParameters = null
    )
    {
        $entryConditions =
            $this->getConditionForTreeNodeDataAndPublication(
                $contentObjectPublication, $treeNodeData, $recordRetrievesParameters->getCondition()
            );
        $recordRetrievesParameters->setCondition($entryConditions);

        return $this->findAssignmentEntriesWithRequests($recordRetrievesParameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int[] $entryIds
     *
     * @return Request[]
     */
    public function findEphorusRequestsForAssignmentEntriesByTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, array $entryIds = []
    )
    {
        return $this->findEphorusRequestsForAssignmentEntries(
            $entryIds, $this->getConditionForTreeNodeDataAndPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithRequestsByTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, Condition $condition = null
    )
    {
        return $this->countAssignmentEntriesWithRequests(
            $this->getConditionForTreeNodeDataAndPublication($contentObjectPublication, $treeNodeData, $condition)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return Condition
     */
    protected function getConditionForTreeNodeDataAndPublication(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, Condition $condition = null
    )
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Entry::class, Entry::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID),
            new StaticConditionVariable($contentObjectPublication->getId())
        );

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $condition = new AndCondition($conditions);

        return $this->getConditionForTreeNodeData($treeNodeData, $condition);
    }

    /**
     * @return string
     */
    protected function getEntryClassName()
    {
        return Entry::class;
    }

}