<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\Repository\AssignmentRepository;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableColumnModel;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
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
        $condition = $this->getTreeNodeDataConditionByIdentifier($treeNodeDataIdentifier);

        return $this->dataClassRepository->count($this->getEntryClassName(), new DataClassCountParameters($condition));
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
        TreeNodeData $treeNodeData, array $userIds, $condition, $offset, $count,
        $orderBy
    )
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));

        $baseClass = User::class_name();

        return $this->findTargetsForEntityTypeAndTreeNodeData(
            \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Entry::ENTITY_TYPE_USER,
            $treeNodeData,
            $this->getTargetEntitiesCondition(User::class_name(), $userIds, $condition),
            $offset,
            $count,
            $orderBy,
            $properties,
            $baseClass,
            $this->getTargetBaseVariable($baseClass)
        );
    }

    /**
     *
     * @param string $entityClass
     * @param array $entityIds
     * @param Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getTargetEntitiesCondition($entityClass, $entityIds = array(), Condition $condition = null)
    {
        $conditions = array();

        !is_null($condition) ? $conditions[] = $condition : null;

        $conditions[] =
            new InCondition(new PropertyConditionVariable($entityClass, DataClass::PROPERTY_ID), $entityIds);

        return new AndCondition($conditions);
    }

    /**
     *
     * @param string $baseClass
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    protected function getTargetBaseVariable($baseClass)
    {
        return new PropertyConditionVariable($baseClass, DataClass::PROPERTY_ID);
    }

    /**
     *
     * @param integer $entityType
     * @param TreeNodeData $treeNodeData
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     * @param DataClassProperties $properties
     * @param string $baseClass
     * @param PropertyConditionVariable $baseVariable
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    protected function findTargetsForEntityTypeAndTreeNodeData(
        $entityType, TreeNodeData $treeNodeData,
        $condition, $offset, $count, $orderBy, DataClassProperties $properties, $baseClass, $baseVariable
    )
    {
        $properties->add(
            new FixedPropertyConditionVariable($baseClass, DataClass::PROPERTY_ID, Entry::PROPERTY_ENTITY_ID)
        );
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE));

        $submittedVariable = new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED);

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::MIN,
                $submittedVariable,
                EntityTableColumnModel::PROPERTY_FIRST_ENTRY_DATE
            )
        );

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::MAX,
                $submittedVariable,
                EntityTableColumnModel::PROPERTY_LAST_ENTRY_DATE
            )
        );

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT,
                $submittedVariable,
                EntityTableColumnModel::PROPERTY_ENTRY_COUNT
            )
        );

        $joins = new Joins();

        $joinConditions = array();

        $joinConditions[] = new EqualityCondition(
            $baseVariable,
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID)
        );

        $joinConditions[] = $this->getTreeNodeDataCondition($treeNodeData);

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );

        $joinCondition = new AndCondition($joinConditions);

        $joins->add(new Join($this->getEntryClassName(), $joinCondition, Join::TYPE_LEFT));

        $group_by = new GroupBy();
        $group_by->add($baseVariable);

        $parameters = new RecordRetrievesParameters(
            $properties,
            $condition,
            $count,
            $offset,
            $orderBy,
            $joins,
            $group_by
        );

        return $this->dataClassRepository->records($baseClass, $parameters);
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
     * @return AndCondition
     */
    protected function getTreeNodeDataAndEntityTypeCondition(TreeNodeData $treeNodeData, $entityType)
    {
        $conditions = array();

        $conditions[] = $this->getTreeNodeDataCondition($treeNodeData);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );

        return new AndCondition($conditions);
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
        $property = new FunctionConditionVariable(
            FunctionConditionVariable::DISTINCT,
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID)
        );

        $parameters = new DataClassCountParameters(
            $this->getTreeNodeDataAndEntityTypeCondition($treeNodeData, $entityType),
            null,
            $property
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
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
        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getFeedbackClassName(),
                new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable($this->getFeedbackClassName(), Feedback::PROPERTY_ENTRY_ID)
                )
            )
        );

        $property = new FunctionConditionVariable(
            FunctionConditionVariable::DISTINCT,
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID)
        );

        $parameters = new DataClassCountParameters(
            $this->getTreeNodeDataAndEntityTypeCondition($treeNodeData, $entityType),
            $joins,
            $property
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
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
        Assignment $assignment, TreeNodeData $treeNodeData,
        $entityType
    )
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                TreeNodeData::class,
                new EqualityCondition(
                    new PropertyConditionVariable(
                        TreeNodeData::class_name(),
                        TreeNodeData::PROPERTY_ID
                    ),
                    new PropertyConditionVariable(
                        $this->getEntryClassName(),
                        \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Entry::PROPERTY_TREE_NODE_ATTEMPT_ID
                    )
                )
            )
        );

        $property = new FunctionConditionVariable(
            FunctionConditionVariable::DISTINCT,
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID)
        );

        $conditions = array();
        $conditions[] = $this->getTreeNodeDataAndEntityTypeCondition($treeNodeData, $entityType);

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED),
            ComparisonCondition::GREATER_THAN,
            new StaticConditionVariable($assignment->get_end_time())
        );
        $condition = new AndCondition($conditions);

        $parameters = new DataClassCountParameters($condition, $joins, $property);

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @param $entityId
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getTreeNodeDataEntityTypeAndIdCondition(
        TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        $conditions = array();

        $conditions[] = $this->getTreeNodeDataCondition($treeNodeData);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entityId)
        );

        return new AndCondition($conditions);
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
        TreeNodeData $treeNodeData, $entityType,
        $entityId, $condition
    )
    {
        $conditions = array();

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = $this->getTreeNodeDataEntityTypeAndIdCondition($treeNodeData, $entityType, $entityId);

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->count($this->getEntryClassName(), new DataClassCountParameters($condition));
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctFeedbackForEntityTypeAndId(
        TreeNodeData $treeNodeData, $entityType,
        $entityId
    )
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getFeedbackClassName(),
                new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable($this->getFeedbackClassName(), Feedback::PROPERTY_ENTRY_ID)
                )
            )
        );

        $parameters = new DataClassCountParameters(
            $this->getTreeNodeDataEntityTypeAndIdCondition($treeNodeData, $entityType, $entityId),
            $joins
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForEntityTypeAndId(TreeNodeData $treeNodeData, $entityType, $entityId)
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getScoreClassName(),
                new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID)
                )
            )
        );

        $parameters = new DataClassCountParameters(
            $this->getTreeNodeDataEntityTypeAndIdCondition($treeNodeData, $entityType, $entityId),
            $joins
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function retrieveAverageScoreForEntityTypeAndId(TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getScoreClassName(),
                new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID)
                )
            )
        );

        $properties = new DataClassProperties();
        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::AVERAGE,
                new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_SCORE),
                AssignmentDataProvider::AVERAGE_SCORE
            )
        );

        $parameters = new RecordRetrieveParameters(
            $properties,
            $this->getTreeNodeDataEntityTypeAndIdCondition($treeNodeData, $entityType, $entityId),
            array(),
            $joins
        );

        $record = $this->dataClassRepository->record($this->getEntryClassName(), $parameters);

        return $record[AssignmentDataProvider::AVERAGE_SCORE];
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
        TreeNodeData $treeNodeData, $entityType,
        $entityId, $condition, $offset, $count, $orderProperty
    )
    {
        $conditions = array();

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = $this->getTreeNodeDataEntityTypeAndIdCondition($treeNodeData, $entityType, $entityId);

        $condition = new AndCondition($conditions);

        $joins = new Joins();

        $joins->add(
            new Join(
                User::class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID),
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_USER_ID)
                )
            )
        );

        $joins->add(
            new Join(
                ContentObject::class_name(),
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID),
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_CONTENT_OBJECT_ID)
                )
            )
        );

        $joins->add(
            new Join(
                $this->getScoreClassName(),
                new EqualityCondition(
                    new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID),
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID)
                ),
                Join::TYPE_LEFT
            )
        );

        $properties = new DataClassProperties();

        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME));

        $properties->add(new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE));
        $properties->add(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION)
        );
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED));
        $properties->add(new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_SCORE));
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_CONTENT_OBJECT_ID));
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_USER_ID));
        $properties->add(new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TYPE));

        $parameters = new RecordRetrievesParameters($properties, $condition, $count, $offset, $orderProperty, $joins);

        return $this->dataClassRepository->records($this->getEntryClassName(), $parameters);
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
        $conditions = array();

        $conditions[] = $this->getTreeNodeDataCondition($treeNodeData);

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );
        $conditions[] = new InCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID),
            $entityIdentifiers
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieves(
            $this->getEntryClassName(), new DataClassRetrievesParameters($condition)
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
        $condition = $this->getTreeNodeDataCondition($treeNodeData);

        return $this->dataClassRepository->retrieves(
            $this->getEntryClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param TreeNodeData $treeNodeData
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    protected function getTreeNodeDataCondition(TreeNodeData $treeNodeData)
    {
        return $this->getTreeNodeDataConditionByIdentifier($treeNodeData->getId());
    }

    /**
     * @param int $treeNodeDataIdentifier
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    protected function getTreeNodeDataConditionByIdentifier($treeNodeDataIdentifier)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                $this->getEntryClassName(),
                \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Entry::PROPERTY_TREE_NODE_DATA_ID
            ),
            new StaticConditionVariable($treeNodeDataIdentifier)
        );
    }
}