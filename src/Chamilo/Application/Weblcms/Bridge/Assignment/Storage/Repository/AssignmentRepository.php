<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Storage\Repository;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\GroupBy;
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
class AssignmentRepository extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository\AssignmentRepository
{
    /**
     *
     * @param integer $contentObjectPublicationIdentifier
     *
     * @return integer
     */
    public function countEntriesForContentObjectPublicationIdentifier($contentObjectPublicationIdentifier)
    {
        return $this->countEntries(
            $this->getContentObjectPublicationConditionByIdentifier($contentObjectPublicationIdentifier)
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $userIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetUsersForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, array $userIds, $condition = null, $offset = null,
        $count = null,
        $orderBy = []
    )
    {
        return $this->findTargetsForEntityType(
            Entry::ENTITY_TYPE_USER,
            $this->getTargetEntitiesCondition(User::class_name(), $userIds, $condition),
            $this->getContentObjectPublicationCondition($contentObjectPublication),
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
     * @param int[] $userIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTargetUsersWithEntriesForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, array $userIds, $condition = null, $offset = null,
        $count = null,
        $orderBy = []
    )
    {
        return $this->findTargetsForEntityTypeWithEntries(
            Entry::ENTITY_TYPE_USER,
            $this->getTargetEntitiesCondition(User::class_name(), $userIds, $condition),
            $this->getContentObjectPublicationCondition($contentObjectPublication),
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

        return $properties;
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param int[] $groupIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetCourseGroupsForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, array $groupIds, $condition = null, $offset = null,
        $count = null,
        $orderBy = []
    )
    {
        return $this->findTargetsForEntityType(
            Entry::ENTITY_TYPE_COURSE_GROUP,
            $this->getTargetEntitiesCondition(CourseGroup::class_name(), $groupIds, $condition),
            $this->getContentObjectPublicationCondition($contentObjectPublication),
            $offset,
            $count,
            $orderBy,
            $this->getDataClassPropertiesForCourseGroup(),
            CourseGroup::class_name(),
            $this->getTargetBaseVariable(CourseGroup::class_name())
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int[] $courseGroupIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTargetCourseGroupsWithEntriesForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, array $courseGroupIds, $condition = null, $offset = null,
        $count = null,
        $orderBy = []
    )
    {
        return $this->findTargetsForEntityTypeWithEntries(
            Entry::ENTITY_TYPE_COURSE_GROUP,
            $this->getTargetEntitiesCondition(CourseGroup::class_name(), $courseGroupIds, $condition),
            $this->getContentObjectPublicationCondition($contentObjectPublication),
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
     * @param int[] $groupIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findTargetPlatformGroupsForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, array $groupIds, $condition = null, $offset = null,
        $count = null,
        $orderBy = []
    )
    {
        return $this->findTargetsForEntityType(
            Entry::ENTITY_TYPE_PLATFORM_GROUP,
            $this->getTargetEntitiesCondition(Group::class_name(), $groupIds, $condition),
            $this->getContentObjectPublicationCondition($contentObjectPublication),
            $offset,
            $count,
            $orderBy,
            $this->getDataClassPropertiesForPlatformGroups(),
            Group::class_name(),
            $this->getTargetBaseVariable(Group::class_name())
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int[] $platformGroupIds
     * @param Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findTargetPlatformGroupsWithEntriesForContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, array $platformGroupIds, $condition = null, $offset = null,
        $count = null,
        $orderBy = []
    )
    {
        return $this->findTargetsForEntityTypeWithEntries(
            Entry::ENTITY_TYPE_PLATFORM_GROUP,
            $this->getTargetEntitiesCondition(Group::class_name(), $platformGroupIds, $condition),
            $this->getContentObjectPublicationCondition($contentObjectPublication),
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
     * @param int $entityType
     *
     * @param int $createdDate
     *
     * @return mixed
     */
    public function countEntriesByContentObjectPublicationWithCreatedDateLargerThan(
        ContentObjectPublication $contentObjectPublication, $entityType, $createdDate
    )
    {
        return $this->countEntriesByEntityTypeWithCreatedDateLargerThan(
            $entityType,
            $createdDate,
            $this->getContentObjectPublicationCondition($contentObjectPublication)
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     *
     * @return integer
     */
    public function countDistinctEntriesByContentObjectPublicationAndEntityType(
        ContentObjectPublication $contentObjectPublication, $entityType
    )
    {
        return $this->countDistinctEntriesByEntityType(
            $entityType, $this->getContentObjectPublicationCondition($contentObjectPublication)
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $entityType
     *
     * @return int
     */
    public function countEntriesByContentObjectPublicationAndEntityType(
        ContentObjectPublication $contentObjectPublication, $entityType
    )
    {
        return $this->countEntriesByEntityType(
            $entityType, $this->getContentObjectPublicationCondition($contentObjectPublication)
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     *
     * @return int
     */
    public function countDistinctLateEntriesByContentObjectPublicationAndEntityType(
        Assignment $assignment, ContentObjectPublication $contentObjectPublication, $entityType
    )
    {
        return $this->countDistinctLateEntriesByEntityType(
            $assignment, $entityType, $this->getContentObjectPublicationCondition($contentObjectPublication)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $entityType
     * @param int $entityId
     *
     * @return int
     */
    public function countLateEntriesByContentObjectPublicationEntityTypeAndId(
        Assignment $assignment, ContentObjectPublication $contentObjectPublication, $entityType, $entityId
    )
    {
        return $this->countLateEntriesByEntityTypeAndId(
            $assignment, $entityType, $entityId, $this->getContentObjectPublicationCondition($contentObjectPublication)
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    public function countEntriesForContentObjectPublicationEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId, Condition $condition = null
    )
    {
        return $this->countEntriesByEntityTypeAndId(
            $entityType, $entityId, $this->getContentObjectPublicationCondition($contentObjectPublication, $condition)
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return integer
     */
    public function countDistinctScoreForContentObjectPublicationEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId
    )
    {
        return parent::countDistinctScoreForEntityTypeAndId(
            $entityType, $entityId, $this->getContentObjectPublicationCondition($contentObjectPublication)
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function retrieveAverageScoreForContentObjectPublicationEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId
    )
    {
        return $this->retrieveAverageScoreForEntityTypeAndId(
            $entityType, $entityId, $this->getContentObjectPublicationCondition($contentObjectPublication)
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     *
     * @return int
     */
    public function retrieveLastScoreForContentObjectPublicationEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId
    )
    {
        return $this->retrieveLastScoreForEntityTypeAndId(
            $entityType, $entityId, $this->getContentObjectPublicationCondition($contentObjectPublication)
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function retrieveEntriesForContentObjectPublicationEntityTypeAndId(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId,
        Condition $condition = null, $offset = null, $count = null, $orderProperty = []
    )
    {
        return $this->retrieveEntriesForEntityTypeAndId(
            $entityType, $entityId, $this->getContentObjectPublicationCondition($contentObjectPublication, $condition),
            $offset, $count,
            $orderProperty
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByContentObjectPublicationEntityTypeAndIdentifiers(
        ContentObjectPublication $contentObjectPublication, $entityType,
        $entityIdentifiers
    )
    {
        return $this->findEntriesByEntityTypeAndIdentifiers(
            $entityType, $entityIdentifiers, $this->getContentObjectPublicationCondition($contentObjectPublication)
        );
    }

    /**
     *
     * @param ContentObjectPublication $contentObjectPublication
     * @param int $entityType
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntriesByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $entityType = null
    )
    {
        return $this->findEntries($this->getContentObjectPublicationCondition($contentObjectPublication), $entityType);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param int $entityType
     * @param int $entityIdentifier
     *
     * @return \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findLastEntryForEntityByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityIdentifier
    )
    {
        return $this->findLastEntryForEntity(
            $entityType, $entityIdentifier, $this->getContentObjectPublicationCondition($contentObjectPublication)
        );
    }

    /**
     * @param int[] $contentObjectPublicationIdentifiers
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findEntryStatisticsByContentObjectPublicationIdentifiers($contentObjectPublicationIdentifiers = [])
    {
        $contentObjectPublicationIdProperty =
            new PropertyConditionVariable(Entry::class, Entry::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID);

        $properties = new DataClassProperties();
        $properties->add($contentObjectPublicationIdProperty);
        $properties->add(new PropertyConditionVariable(Entry::class, Entry::PROPERTY_ENTITY_TYPE));

        $groupBy = new GroupBy();
        $groupBy->add($contentObjectPublicationIdProperty);

        $joins = new Joins();
        $joins->add(
            new Join(
                Publication::class,
                new AndCondition(
                    [
                        new EqualityCondition(
                            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLICATION_ID),
                            $contentObjectPublicationIdProperty
                        ),
                        new EqualityCondition(
                            new PropertyConditionVariable(Publication::class, Publication::PROPERTY_ENTITY_TYPE),
                            new PropertyConditionVariable(Entry::class, Entry::PROPERTY_ENTITY_TYPE)
                        )
                    ]
                )
            )
        );

        return $this->findEntryStatistics(
            $properties, $this->getContentObjectPublicationConditionByIdentifiers($contentObjectPublicationIdentifiers),
            $joins, $groupBy
        );
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param $entityType
     * @param $entityId
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function findEntryStatisticsForEntityByContentObjectPublication(
        ContentObjectPublication $contentObjectPublication, $entityType, $entityId
    )
    {
        return $this->findEntryStatisticsForEntity(
            $entityType, $entityId, $this->getContentObjectPublicationCondition($contentObjectPublication)
        );
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getContentObjectPublicationCondition(
        ContentObjectPublication $contentObjectPublication, Condition $condition = null
    )
    {
        return $this->getContentObjectPublicationConditionByIdentifier($contentObjectPublication->getId(), $condition);
    }

    /**
     * @param int $contentObjectPublicationIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getContentObjectPublicationConditionByIdentifier(
        $contentObjectPublicationIdentifier, Condition $condition = null
    )
    {
        $contentObjectPublicationCondition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getEntryClassName(),
                Entry::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID
            ),
            new StaticConditionVariable($contentObjectPublicationIdentifier)
        );

        $conditions = array();

        ($condition instanceof Condition) ? $conditions[] = $condition : null;

        $conditions[] = $contentObjectPublicationCondition;

        return new AndCondition($conditions);
    }

    /**
     * @param int[] $contentObjectPublicationIdentifiers
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getContentObjectPublicationConditionByIdentifiers(
        $contentObjectPublicationIdentifiers = [], Condition $condition = null
    )
    {
        $contentObjectPublicationCondition = new InCondition(
            new PropertyConditionVariable(
                $this->getEntryClassName(),
                Entry::PROPERTY_CONTENT_OBJECT_PUBLICATION_ID
            ),
            $contentObjectPublicationIdentifiers
        );

        $conditions = array();

        ($condition instanceof Condition) ? $conditions[] = $condition : null;

        $conditions[] = $contentObjectPublicationCondition;

        return new AndCondition($conditions);
    }

    /**
     * @return string
     */
    protected function getEntryClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry::class;
    }

    /**
     * @return string
     */
    protected function getEntryAttachmentClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\EntryAttachment::class;
    }

    /**
     * @return string
     */
    protected function getScoreClassName()
    {
        return \Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Score::class;
    }
}