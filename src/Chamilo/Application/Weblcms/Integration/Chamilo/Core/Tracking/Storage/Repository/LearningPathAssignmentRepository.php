<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\Repository;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
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
class LearningPathAssignmentRepository extends
    \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathAssignmentRepository
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
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersForTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, array $userIds,
        $condition = null, $offset = null, $count = null,
        $orderBy = []
    )
    {
        return $this->findTargetsForEntityType(
            \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Entry::ENTITY_TYPE_USER,
            $this->getTargetEntitiesCondition(User::class_name(), $userIds, $condition),
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData),
            $offset,
            $count,
            $orderBy,
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
            \Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Entry::ENTITY_TYPE_USER,
            $this->getTargetEntitiesCondition(User::class_name(), $userIds, $condition),
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData),
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
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countFeedbackForTreeNodeDataByEntityTypeAndEntityId(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return $this->countFeedbackByEntityTypeAndEntityId(
            $entityType, $entityId,
            $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
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
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param TreeNodeData $treeNodeData
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctFeedbackByTreeNodeDataAndEntityType(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType
    )
    {
        return $this->countDistinctFeedbackByEntityType(
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
    public function countDistinctFeedbackForTreeNodeDataEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityId
    )
    {
        return parent::countDistinctFeedbackForEntityTypeAndId(
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
        return $this->findEntries($this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData));
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData $treeNodeData
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\Entry|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findLastEntryForEntityByTreeNodeData(
        ContentObjectPublication $contentObjectPublication, TreeNodeData $treeNodeData, $entityType, $entityIdentifier
    )
    {
        return $this->findLastEntryForEntity(
            $entityType, $entityIdentifier, $this->getTreeNodeDataConditionByPublication($contentObjectPublication, $treeNodeData)
        );
    }

    /**
     * @return string
     */
    protected function getEntryClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Entry::class;
    }

    /**
     * @return string
     */
    protected function getFeedbackClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Feedback::class;
    }

    /**
     * @return string
     */
    protected function getNoteClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Note::class;
    }

    /**
     * @return string
     */
    protected function getScoreClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\Score::class;
    }

    /**
     * @return string
     */
    protected function getEntryAttachmentClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPath\Assignment\EntryAttachment::class;
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