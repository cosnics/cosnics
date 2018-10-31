<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\Repository\AssignmentRepository;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Abstract service that can be used as a base for the LearningPathAssignmentRepository
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class LearningPathAssignmentRepository extends AssignmentRepository
{
    /**
     *
     * @param integer $treeNodeDataIdentifier
     *
     * @return integer
     */
    public function countEntriesForTreeNodeDataIdentifier($treeNodeDataIdentifier)
    {
        return $this->countEntries($this->getTreeNodeDataConditionByIdentifier($treeNodeDataIdentifier));
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param int[] $userIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersForTreeNodeData(
        TreeNodeData $treeNodeData, array $userIds, $condition = null, $offset = null, $count = null,
        $orderBy = []
    )
    {
        return $this->findTargetsForEntityType(
            \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry::ENTITY_TYPE_USER,
            $this->getTargetEntitiesCondition(User::class_name(), $userIds, $condition),
            $this->getTreeNodeDataCondition($treeNodeData),
            $offset,
            $count,
            $orderBy,
            $this->getDataClassPropertiesForUser(),
            User::class_name(),
            $this->getTargetBaseVariable(User::class_name())
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @param int[] $userIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersWithEntriesForTreeNodeData(
        TreeNodeData $treeNodeData, array $userIds, $condition = null, $offset = null, $count = null,
        $orderBy = []
    )
    {
        return $this->findTargetsForEntityTypeWithEntries(
            \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry::ENTITY_TYPE_USER,
            $this->getTargetEntitiesCondition(User::class_name(), $userIds, $condition),
            $this->getTreeNodeDataCondition($treeNodeData),
            $offset,
            $count,
            $orderBy,
            $this->getDataClassPropertiesForUser(),
            User::class_name(),
            $this->getTargetBaseVariable(User::class_name())
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     */
    protected function getDataClassPropertiesForUser()
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));

        return $properties;
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
        TreeNodeData $treeNodeData,
        $entityType, $entityId
    )
    {
        return $this->countFeedbackByEntityTypeAndEntityId(
            $entityType, $entityId, $this->getTreeNodeDataCondition($treeNodeData)
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByTreeNodeDataAndEntityType(TreeNodeData $treeNodeData, $entityType)
    {
        return $this->countDistinctEntriesByEntityType($entityType, $this->getTreeNodeDataCondition($treeNodeData));
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByTreeNodeDataAndEntityType(TreeNodeData $treeNodeData, $entityType)
    {
        return $this->countDistinctFeedbackByEntityType($entityType, $this->getTreeNodeDataCondition($treeNodeData));
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return int
     */
    public function countDistinctLateEntriesByTreeNodeDataAndEntityType(
        Assignment $assignment, TreeNodeData $treeNodeData, $entityType
    )
    {
        return $this->countDistinctLateEntriesByEntityType(
            $assignment, $entityType, $this->getTreeNodeDataCondition($treeNodeData)
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countEntriesForTreeNodeDataEntityTypeAndId(
        TreeNodeData $treeNodeData, $entityType, $entityId, Condition $condition = null
    )
    {
        return $this->countEntriesByEntityTypeAndId(
            $entityType, $entityId, $this->getTreeNodeDataCondition($treeNodeData, $condition)
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
        TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return parent::countDistinctFeedbackForEntityTypeAndId(
            $entityType, $entityId, $this->getTreeNodeDataCondition($treeNodeData)
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForTreeNodeDataEntityTypeAndId(TreeNodeData $treeNodeData, $entityType, $entityId)
    {
        return parent::countDistinctScoreForEntityTypeAndId(
            $entityType, $entityId, $this->getTreeNodeDataCondition($treeNodeData)
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function retrieveAverageScoreForTreeNodeDataEntityTypeAndId(
        TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return $this->retrieveAverageScoreForEntityTypeAndId(
            $entityType, $entityId, $this->getTreeNodeDataCondition($treeNodeData)
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function retrieveLastScoreForTreeNodeDataEntityTypeAndId(
        TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return $this->retrieveLastScoreForEntityTypeAndId(
            $entityType, $entityId, $this->getTreeNodeDataCondition($treeNodeData)
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function retrieveEntriesForTreeNodeDataEntityTypeAndId(
        TreeNodeData $treeNodeData, $entityType, $entityId,
        Condition $condition = null, $offset = null, $count = null, $orderProperty = []
    )
    {
        return $this->retrieveEntriesForEntityTypeAndId(
            $entityType, $entityId, $this->getTreeNodeDataCondition($treeNodeData, $condition), $offset, $count,
            $orderProperty
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeDataEntityTypeAndIdentifiers(
        TreeNodeData $treeNodeData, $entityType,
        $entityIdentifiers
    )
    {
        return $this->findEntriesByEntityTypeAndIdentifiers(
            $entityType, $entityIdentifiers, $this->getTreeNodeDataCondition($treeNodeData)
        );
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeData(TreeNodeData $treeNodeData)
    {
        return $this->findEntries($this->getTreeNodeDataCondition($treeNodeData));
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findLastEntryForEntityByTreeNodeData(TreeNodeData $treeNodeData, $entityType, $entityIdentifier)
    {
        return $this->findLastEntryForEntity(
            $entityType, $entityIdentifier, $this->getTreeNodeDataCondition($treeNodeData)
        );
    }

    /**
     * @param TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getTreeNodeDataCondition(TreeNodeData $treeNodeData, Condition $condition = null)
    {
        return $this->getTreeNodeDataConditionByIdentifier($treeNodeData->getId(), $condition);
    }

    /**
     * @param int $treeNodeDataIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getTreeNodeDataConditionByIdentifier($treeNodeDataIdentifier, Condition $condition = null)
    {
        $treeNodeDataCondition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getEntryClassName(),
                \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\DataClass\Entry::PROPERTY_TREE_NODE_DATA_ID
            ),
            new StaticConditionVariable($treeNodeDataIdentifier)
        );

        $conditions = array();

        ($condition instanceof Condition) ? $conditions[] = $condition : null;

        $conditions[] = $treeNodeDataCondition;

        return new AndCondition($conditions);
    }
}
