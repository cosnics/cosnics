<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\Repository;

use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\LearningPathAttemptEntryRelation;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Manages the entities for the learning path assignment submissions
 *
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentRepository extends
    \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Bridge\Storage\Repository\AssignmentRepository
{
    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param integer $treeNodeDataIdentifier
     *
     * @return integer
     */
    public function countEntriesForTreeNodeDataIdentifier(
        ContentObjectPublication $contentObjectPublication, $treeNodeDataIdentifier
    )
    {
        return $this->countEntries(
            $this->getTreeNodeDataConditionByIdentifierAndPublication(
                $contentObjectPublication, $treeNodeDataIdentifier
            )
        );
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param int[] $userIds
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersForTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, array $userIds,
        FilterParameters $filterParameters
    )
    {
        return $this->findTargetsForEntityType(
            Entry::ENTITY_TYPE_USER,
            $this->getTargetEntitiesCondition(User::class_name(), $userIds),
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData),
            $filterParameters,
            $this->getDataClassPropertiesForUser(),
            User::class_name(),
            $this->getTargetBaseVariable(User::class_name())
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     *
     * @param int[] $userIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTargetUsersWithEntriesForTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, array $userIds,
        $condition = null, $offset = null, $count = null,
        $orderBy = []
    )
    {
        return $this->findTargetsForEntityTypeWithEntries(
            Entry::ENTITY_TYPE_USER,
            $this->getTargetEntitiesCondition(User::class_name(), $userIds, $condition),
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData),
            $offset,
            $count,
            $orderBy,
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
        $properties->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE));

        return $properties;
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int[] $groupIds
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetCourseGroupsForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, array $groupIds,
        FilterParameters $filterParameters
    )
    {
        return $this->findTargetsForEntityType(
            Entry::ENTITY_TYPE_COURSE_GROUP,
            $this->getTargetEntitiesCondition(CourseGroup::class_name(), $groupIds),
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData),
            $filterParameters,
            $this->getDataClassPropertiesForCourseGroup(),
            CourseGroup::class_name(),
            $this->getTargetBaseVariable(CourseGroup::class_name())
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int[] $courseGroupIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTargetCourseGroupsWithEntriesForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData,
        array $courseGroupIds, $condition = null, $offset = null, $count = null,
        $orderBy = []
    )
    {
        return $this->findTargetsForEntityTypeWithEntries(
            Entry::ENTITY_TYPE_COURSE_GROUP,
            $this->getTargetEntitiesCondition(CourseGroup::class_name(), $courseGroupIds, $condition),
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData),
            $offset,
            $count,
            $orderBy,
            CourseGroup::class_name(),
            $this->getTargetBaseVariable(CourseGroup::class_name())
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     */
    protected function getDataClassPropertiesForCourseGroup()
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(CourseGroup::class_name(), CourseGroup::PROPERTY_NAME));

        return $properties;
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int[] $groupIds
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetPlatformGroupsForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, array $groupIds,
        FilterParameters $filterParameters
    )
    {
        return $this->findTargetsForEntityType(
            Entry::ENTITY_TYPE_PLATFORM_GROUP,
            $this->getTargetEntitiesCondition(Group::class_name(), $groupIds),
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData),
            $filterParameters,
            $this->getDataClassPropertiesForPlatformGroups(),
            Group::class_name(),
            $this->getTargetBaseVariable(Group::class_name())
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int[] $platformGroupIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTargetPlatformGroupsWithEntriesForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, array $platformGroupIds,
        $condition = null, $offset = null,
        $count = null,
        $orderBy = []
    )
    {
        return $this->findTargetsForEntityTypeWithEntries(
            Entry::ENTITY_TYPE_PLATFORM_GROUP,
            $this->getTargetEntitiesCondition(Group::class_name(), $platformGroupIds, $condition),
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData),
            $offset,
            $count,
            $orderBy,
            Group::class_name(),
            $this->getTargetBaseVariable(Group::class_name())
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties
     */
    protected function getDataClassPropertiesForPlatformGroups()
    {
        $properties = new DataClassProperties();
        $properties->add(new PropertyConditionVariable(Group::class_name(), Group::PROPERTY_NAME));

        return $properties;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByTreeNodeDataAndEntityType(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType
    )
    {
        return $this->countDistinctEntriesByEntityType(
            $entityType, $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return int
     */
    public function countDistinctLateEntriesByTreeNodeDataAndEntityType(
        Assignment $assignment, ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData,
        $entityType
    )
    {
        return $this->countDistinctLateEntriesByEntityType(
            $assignment, $entityType,
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countEntriesForTreeNodeDataEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId,
        Condition $condition = null
    )
    {
        return $this->countEntriesByEntityTypeAndId(
            $entityType, $entityId,
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData, $condition)
        );
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForTreeNodeDataEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return parent::countDistinctScoreForEntityTypeAndId(
            $entityType, $entityId,
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function retrieveAverageScoreForTreeNodeDataEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return $this->retrieveAverageScoreForEntityTypeAndId(
            $entityType, $entityId,
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function retrieveLastScoreForTreeNodeDataEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return $this->retrieveLastScoreForEntityTypeAndId(
            $entityType, $entityId,
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
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
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId,
        Condition $condition = null, $offset = null, $count = null, $orderProperty = []
    )
    {
        return $this->retrieveEntriesForEntityTypeAndId(
            $entityType, $entityId,
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData, $condition), $offset,
            $count,
            $orderProperty
        );
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeDataEntityTypeAndIdentifiers(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType,
        $entityIdentifiers
    )
    {
        return $this->findEntriesByEntityTypeAndIdentifiers(
            $entityType, $entityIdentifiers,
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData
    )
    {
        return $this->findEntries(
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findLastEntryForEntityByTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityIdentifier
    )
    {
        return $this->findLastEntryForEntity(
            $entityType, $entityIdentifier,
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\LearningPathAttemptEntryRelation $learningPathAttemptEntryRelation
     *
     * @return bool
     */
    public function createLearningPathAttemptEntryRelation(
        LearningPathAttemptEntryRelation $learningPathAttemptEntryRelation
    )
    {
        return $this->dataClassRepository->create($learningPathAttemptEntryRelation);
    }

    /**
     * @param int $learningPathAttemptId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findEntryForLearningPathAttempt(int $learningPathAttemptId)
    {
        $joins = new Joins();
        $joins->add(
            new Join(
                LearningPathAttemptEntryRelation::class,
                new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable(
                        LearningPathAttemptEntryRelation::class, LearningPathAttemptEntryRelation::PROPERTY_ENTRY_ID
                    )
                )
            )
        );

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathAttemptEntryRelation::class, LearningPathAttemptEntryRelation::PROPERTY_TREE_NODE_ATTEMPT_ID
            ),
            new StaticConditionVariable($learningPathAttemptId)
        );

        $parameters = new DataClassRetrieveParameters($condition, [], $joins);

        return $this->dataClassRepository->retrieve($this->getEntryClassName(), $parameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|LearningPathTreeNodeAttempt[]
     */
    public function findLearningPathAttemptsByEntry(Entry $entry)
    {
        $joins = new Joins();
        $joins->add(
            new Join(
                LearningPathAttemptEntryRelation::class,
                new EqualityCondition(
                    new PropertyConditionVariable(
                        LearningPathTreeNodeAttempt::class, LearningPathTreeNodeAttempt::PROPERTY_ID
                    ),
                    new PropertyConditionVariable(
                        LearningPathAttemptEntryRelation::class, LearningPathAttemptEntryRelation::PROPERTY_TREE_NODE_ATTEMPT_ID
                    )
                )
            )
        );

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                LearningPathAttemptEntryRelation::class, LearningPathAttemptEntryRelation::PROPERTY_ENTRY_ID
            ),
            new StaticConditionVariable($entry->getId())
        );

        $parameters = new DataClassRetrievesParameters($condition, null, null, [], $joins);

        return $this->dataClassRepository->retrieves(LearningPathTreeNodeAttempt::class, $parameters);
    }

    /**
     * @return string
     */
    protected function getEntryClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Entry::class;
    }

    /**
     * @return string
     */
    protected function getScoreClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\Score::class;
    }

    /**
     * @return string
     */
    protected function getEntryAttachmentClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\LearningPath\Assignment\Storage\DataClass\EntryAttachment::class;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getTreeNodeDataConditionByPublication(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, Condition $condition = null
    )
    {
        return $this->getTreeNodeDataConditionByIdentifierAndPublication(
            $contentObjectPublication, $treeNodeData->getId(), $condition
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $treeNodeDataIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getTreeNodeDataConditionByIdentifierAndPublication(
        ContentObjectPublication $contentObjectPublication, $treeNodeDataIdentifier, Condition $condition = null
    )
    {
        $contentObjectPublicationCondition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getEntryClassName(),
                Entry::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID
            ),
            new StaticConditionVariable($contentObjectPublication->getId())
        );

        $conditions = array();

        ($condition instanceof Condition) ? $conditions[] = $condition : null;

        $conditions[] = $contentObjectPublicationCondition;

        $condition = new AndCondition($conditions);

        return $this->getTreeNodeDataConditionByIdentifier($treeNodeDataIdentifier, $condition);
    }
}
