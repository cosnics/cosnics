<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableColumnModel;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
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
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\FunctionConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Abstract class to provide common functionality to handle assignment entries, feedback, scores and notes
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class AssignmentRepository
{
    public const AVERAGE_SCORE = 'average_score';

    public const ENTRIES_COUNT = 'entries_count';

    public const LAST_ENTRY_SUBMITTED_DATE = 'last_entry_submitted_date';

    public const MAXIMUM_SCORE = 'maximum_score';

    public const MINIMUM_SCORE = 'minimum_score';

    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * LearningPathAssignmentRepository constructor.
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param int[] $contentObjectIds
     *
     * @return int
     */
    public function countContentObjectsUsedAsEntryByContentObjectIds($contentObjectIds = [])
    {
        $condition = new InCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_CONTENT_OBJECT_ID),
            $contentObjectIds
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), new DataClassCountParameters($condition));
    }

    /**
     *
     * @param int $entityType
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    protected function countDistinctEntriesByEntityType($entityType, Condition $condition = null)
    {
        $property = new FunctionConditionVariable(
            FunctionConditionVariable::DISTINCT,
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID)
        );

        $parameters = new DataClassCountParameters(
            $this->getEntityTypeCondition($entityType, $condition), null, $property
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param int $entityType
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    protected function countDistinctLateEntriesByEntityType(
        Assignment $assignment, $entityType, Condition $condition = null
    )
    {
        $property = new FunctionConditionVariable(
            FunctionConditionVariable::DISTINCT,
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID)
        );

        $conditions = [];
        $conditions[] = $this->getEntityTypeCondition($entityType, $condition);

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($assignment->get_end_time())
        );

        $condition = new AndCondition($conditions);
        $parameters = new DataClassCountParameters($condition, null, $property);

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param int $entityType
     * @param int $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    protected function countDistinctScoreForEntityTypeAndId($entityType, $entityId, Condition $condition = null)
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getScoreClassName(), new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID)
                )
            )
        );

        $property = new FunctionConditionVariable(
            FunctionConditionVariable::DISTINCT,
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID)
        );

        $parameters = new DataClassCountParameters(
            $this->getEntityTypeAndIdCondition($entityType, $entityId, $condition), $joins, $property
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    protected function countEntries(Condition $condition)
    {
        return $this->dataClassRepository->count($this->getEntryClassName(), new DataClassCountParameters($condition));
    }

    /**
     *
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    protected function countEntriesByEntityType($entityType, Condition $condition = null)
    {
        $condition = $this->getEntityTypeCondition($entityType, $condition);

        return $this->dataClassRepository->count($this->getEntryClassName(), new DataClassCountParameters($condition));
    }

    /**
     *
     * @param int $entityType
     * @param int $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    protected function countEntriesByEntityTypeAndId($entityType, $entityId, Condition $condition = null)
    {
        $condition = $this->getEntityTypeAndIdCondition($entityType, $entityId, $condition);

        return $this->dataClassRepository->count($this->getEntryClassName(), new DataClassCountParameters($condition));
    }

    /**
     * @param int $entityType
     * @param int $createdDate
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    public function countEntriesByEntityTypeWithCreatedDateLargerThan(
        $entityType, $createdDate, Condition $condition = null
    )
    {
        $conditions = [];

        $conditions[] = $this->getEntityTypeCondition($entityType, $condition);
        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($createdDate)
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->count($this->getEntryClassName(), new DataClassCountParameters($condition));
    }

    /**
     * @param int[] $attachmentIds
     *
     * @return int
     */
    public function countEntryAttachmentsByAttachmentIds($attachmentIds = [])
    {
        $condition = new InCondition(
            new PropertyConditionVariable(
                $this->getEntryAttachmentClassName(), EntryAttachment::PROPERTY_ATTACHMENT_ID
            ), $attachmentIds
        );

        return $this->dataClassRepository->count(
            $this->getEntryAttachmentClassName(), new DataClassCountParameters($condition)
        );
    }

    /**
     * @param int $userId
     *
     * @return int
     */
    public function countEntryAttachmentsByUserId($userId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($userId)
        );

        $joins = new Joins();
        $joins->add(
            new Join(
                ContentObject::class, new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    new PropertyConditionVariable(
                        $this->getEntryAttachmentClassName(), EntryAttachment::PROPERTY_ATTACHMENT_ID
                    )
                )
            )
        );

        return $this->dataClassRepository->count(
            $this->getEntryAttachmentClassName(), new DataClassCountParameters($condition, $joins)
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param int $entityType
     * @param int $entityId
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    protected function countLateEntriesByEntityTypeAndId(
        Assignment $assignment, $entityType, $entityId, Condition $condition = null
    )
    {
        $conditions = [];
        $conditions[] = $this->getEntityTypeAndIdCondition($entityType, $entityId, $condition);

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED),
            ComparisonCondition::GREATER_THAN, new StaticConditionVariable($assignment->get_end_time())
        );

        $condition = new AndCondition($conditions);
        $parameters = new DataClassCountParameters($condition);

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return bool
     */
    public function createEntry(Entry $entry)
    {
        return $this->dataClassRepository->create($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     *
     * @return bool
     */
    public function createEntryAttachment(EntryAttachment $entryAttachment)
    {
        return $this->dataClassRepository->create($entryAttachment);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score $score
     *
     * @return bool
     */
    public function createScore(Score $score)
    {
        return $this->dataClassRepository->create($score);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return bool
     */
    public function deleteAttachmentsForEntry(Entry $entry)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryAttachmentClassName(), EntryAttachment::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );

        return $this->dataClassRepository->deletes($this->getEntryAttachmentClassName(), $condition);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return bool
     */
    public function deleteEntry(Entry $entry)
    {
        return $this->dataClassRepository->delete($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     *
     * @return bool
     */
    public function deleteEntryAttachment(EntryAttachment $entryAttachment)
    {
        return $this->dataClassRepository->delete($entryAttachment);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return bool
     */
    public function deleteScoreForEntry(Entry $entry)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );

        return $this->dataClassRepository->deletes($this->getScoreClassName(), $condition);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Doctrine\Common\Collections\ArrayCollection | EntryAttachment[]
     */
    public function findAttachmentsByEntry(Entry $entry)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryAttachmentClassName(), EntryAttachment::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );

        return $this->dataClassRepository->retrieves(
            $this->getEntryAttachmentClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $entityType
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findEntries(Condition $condition, $entityType = null)
    {
        if (!empty($entityType))
        {
            $condition = $this->getEntityTypeCondition($entityType, $condition);
        }

        return $this->dataClassRepository->retrieves(
            $this->getEntryClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return \Doctrine\Common\Collections\ArrayCollection | Entry[]
     */
    public function findEntriesByContentObjectId(ContentObject $contentObject)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_CONTENT_OBJECT_ID),
            new StaticConditionVariable($contentObject->getId())
        );

        return $this->dataClassRepository->retrieves(
            $this->getEntryClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     *
     * @param int $entityType
     * @param int $entityIdentifiers
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function findEntriesByEntityTypeAndIdentifiers(
        $entityType, $entityIdentifiers, Condition $condition
    )
    {
        $conditions = [];

        ($condition instanceof Condition) ? $conditions[] = $condition : null;

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );
        $conditions[] = new InCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID), $entityIdentifiers
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieves(
            $this->getEntryClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param int $attachmentId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass |
     *     EntryAttachment
     */
    public function findEntryAttachmentByEntryAndAttachmentId(Entry $entry, $attachmentId)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryAttachmentClassName(), EntryAttachment::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getEntryAttachmentClassName(), EntryAttachment::PROPERTY_ATTACHMENT_ID
            ), new StaticConditionVariable($attachmentId)
        );

        $condition = new AndCondition($conditions);

        return $this->dataClassRepository->retrieve(
            $this->getEntryAttachmentClassName(), new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param $entryAttachmentId
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass | EntryAttachment
     */
    public function findEntryAttachmentById($entryAttachmentId)
    {
        return $this->dataClassRepository->retrieveById($this->getEntryAttachmentClassName(), $entryAttachmentId);
    }

    /**
     * @param int $attachmentId
     *
     * @return \Doctrine\Common\Collections\ArrayCollection | EntryAttachment[]
     */
    public function findEntryAttachmentsByAttachmentId($attachmentId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getEntryAttachmentClassName(), EntryAttachment::PROPERTY_ATTACHMENT_ID
            ), new StaticConditionVariable($attachmentId)
        );

        return $this->dataClassRepository->retrieves(
            $this->getEntryAttachmentClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @param int $userId
     *
     * @return \Doctrine\Common\Collections\ArrayCollection | EntryAttachment[]
     */
    public function findEntryAttachmentsByUserId($userId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($userId)
        );

        $joins = new Joins();
        $joins->add(
            new Join(
                ContentObject::class, new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    new PropertyConditionVariable(
                        $this->getEntryAttachmentClassName(), EntryAttachment::PROPERTY_ATTACHMENT_ID
                    )
                )
            )
        );

        return $this->dataClassRepository->retrieves(
            $this->getEntryAttachmentClassName(), new DataClassRetrievesParameters(
                condition: $condition, joins: $joins
            )
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\RetrieveProperties $retrieveProperties
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Joins|null $joins
     * @param \Chamilo\Libraries\Storage\Query\GroupBy $groupBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findEntryStatistics(
        RetrieveProperties $retrieveProperties = null, Condition $condition = null, Joins $joins = null,
        GroupBy $groupBy = null
    )
    {
        if (!$retrieveProperties instanceof RetrieveProperties)
        {
            $retrieveProperties = new RetrieveProperties();
        }

        $retrieveProperties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT,
                new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID), self::ENTRIES_COUNT
            )
        );

        $retrieveProperties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::MAX,
                new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED),
                self::LAST_ENTRY_SUBMITTED_DATE
            )
        );

        $retrieveProperties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::AVERAGE,
                new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_SCORE), self::AVERAGE_SCORE
            )
        );

        $retrieveProperties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::MIN,
                new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_SCORE), self::MINIMUM_SCORE
            )
        );

        $retrieveProperties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::MAX,
                new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_SCORE), self::MAXIMUM_SCORE
            )
        );

        if (!$joins instanceof Joins)
        {
            $joins = new Joins();
        }

        $joins->add(
            new Join(
                $this->getScoreClassName(), new EqualityCondition(
                new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID),
                new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID)
            ), Join::TYPE_LEFT
            )
        );

        return $this->dataClassRepository->records(
            $this->getEntryClassName(), new RecordRetrievesParameters(
                retrieveProperties: $retrieveProperties, condition: $condition, joins: $joins, groupBy: $groupBy
            )
        );
    }

    /**
     * Returns the statistics for a single entity
     *
     * @param int $entityType
     * @param int $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findEntryStatisticsForEntity($entityType, $entityId, Condition $condition = null)
    {
        $entryStatistics = $this->findEntryStatistics(
            new RetrieveProperties(), $this->getEntityTypeAndIdCondition($entityType, $entityId, $condition)
        );

        return $entryStatistics[0];
    }

    /**
     * @param int $entityType
     * @param int $entityIdentifier
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return Entry | DataClass
     */
    protected function findLastEntryForEntity($entityType, $entityIdentifier, Condition $condition = null)
    {
        $condition = $this->getEntityTypeAndIdCondition($entityType, $entityIdentifier, $condition);

        $retrieveParameters = new DataClassRetrieveParameters(
            $condition, new OrderBy([
                new OrderProperty(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED), SORT_DESC
                )
            ])
        );

        return $this->dataClassRepository->retrieve($this->getEntryClassName(), $retrieveParameters);
    }

    /**
     * @param int $scoreIdentifier
     *
     * @return Score|DataClass
     */
    public function findScoreByIdentifier($scoreIdentifier)
    {
        return $this->dataClassRepository->retrieveById($this->getScoreClassName(), $scoreIdentifier);
    }

    /**
     * @param int $entityType
     * @param Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $joinCondition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     * @param RetrieveProperties $properties
     * @param string $baseClass
     * @param PropertyConditionVariable $baseVariable
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $havingCondition
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function findTargetsForEntityType(
        $entityType, Condition $condition = null, Condition $joinCondition = null, $offset, $count, $orderBy,
        RetrieveProperties $properties, $baseClass, $baseVariable, Condition $havingCondition = null
    )
    {
        $properties->add(
            new PropertyConditionVariable($baseClass, DataClass::PROPERTY_ID, Entry::PROPERTY_ENTITY_ID)
        );

        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE));

        $submittedVariable = new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED);

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::MIN, $submittedVariable, EntityTableColumnModel::PROPERTY_FIRST_ENTRY_DATE
            )
        );

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::MAX, $submittedVariable, EntityTableColumnModel::PROPERTY_LAST_ENTRY_DATE
            )
        );

        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT, $submittedVariable, EntityTableColumnModel::PROPERTY_ENTRY_COUNT
            )
        );

        $joins = new Joins();

        $joinConditions = [];

        $joinConditions[] = new EqualityCondition(
            $baseVariable, new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID)
        );

        ($joinCondition instanceof Condition) ? $joinConditions[] = $joinCondition : null;

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );

        $joinCondition = new AndCondition($joinConditions);

        $joins->add(new Join($this->getEntryClassName(), $joinCondition, Join::TYPE_LEFT));

        $group_by = new GroupBy();
        $group_by->add($baseVariable);

        $parameters = new RecordRetrievesParameters(
            $properties, $condition, $count, $offset, $orderBy, $joins, $group_by, $havingCondition
        );

        return $this->dataClassRepository->records($baseClass, $parameters);
    }

    /**
     * @param int $entityType
     * @param Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $joinCondition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     * @param RetrieveProperties $properties
     * @param string $baseClass
     * @param PropertyConditionVariable $baseVariable
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     *
     * TODO: changed from records to dataclass so remove properties in calls
     */
    protected function findTargetsForEntityTypeWithEntries(
        $entityType, Condition $condition = null, Condition $joinCondition = null, $offset, $count, $orderBy,
        RetrieveProperties $properties, $baseClass, $baseVariable
    )
    {
        $submittedVariable = new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED);

        $havingCondition = new ComparisonCondition(
            new FunctionConditionVariable(
                FunctionConditionVariable::COUNT, $submittedVariable
            ), ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable(1)
        );

        $joinConditions = [];

        $joinConditions[] = new EqualityCondition(
            $baseVariable, new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID)
        );

        ($joinCondition instanceof Condition) ? $joinConditions[] = $joinCondition : null;

        $joinConditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );

        $joinCondition = new AndCondition($joinConditions);

        $joins = new Joins();
        $joins->add(new Join($this->getEntryClassName(), $joinCondition, Join::TYPE_LEFT));

        $group_by = new GroupBy();
        $group_by->add($baseVariable);

        $parameters = new DataClassRetrievesParameters(
            $condition, $count, $offset, $orderBy, $joins, $group_by, $havingCondition
        );

        return $this->dataClassRepository->retrieves($baseClass, $parameters);
    }

    /**
     *
     * @param int $entityType
     * @param $entityId
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getEntityTypeAndIdCondition($entityType, $entityId, Condition $condition = null)
    {
        $conditions = [];

        ($condition instanceof Condition) ? $conditions[] = $condition : null;

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
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getEntityTypeCondition($entityType, Condition $condition = null)
    {
        $conditions = [];

        ($condition instanceof Condition) ? $conditions[] = $condition : null;

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );

        return new AndCondition($conditions);
    }

    /**
     * @return string
     */
    abstract protected function getEntryAttachmentClassName();

    /**
     * @return string
     */
    abstract protected function getEntryClassName();

    /**
     * @return string
     */
    abstract protected function getScoreClassName();

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
     * @param string $entityClass
     * @param array $entityIds
     * @param Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getTargetEntitiesCondition($entityClass, $entityIds = [], Condition $condition = null)
    {
        $conditions = [];

        !is_null($condition) ? $conditions[] = $condition : null;

        $conditions[] =
            new InCondition(new PropertyConditionVariable($entityClass, DataClass::PROPERTY_ID), $entityIds);

        return new AndCondition($conditions);
    }

    /**
     *
     * @param int $entityType
     * @param int $entityId
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    protected function retrieveAverageScoreForEntityTypeAndId($entityType, $entityId, Condition $condition)
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getScoreClassName(), new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID)
                )
            )
        );

        $properties = new RetrieveProperties();
        $properties->add(
            new FunctionConditionVariable(
                FunctionConditionVariable::AVERAGE,
                new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_SCORE),
                AssignmentDataProvider::AVERAGE_SCORE
            )
        );

        $parameters = new RecordRetrieveParameters(
            $properties, $this->getEntityTypeAndIdCondition($entityType, $entityId, $condition), null, $joins
        );

        $record = $this->dataClassRepository->record($this->getEntryClassName(), $parameters);

        return $record[AssignmentDataProvider::AVERAGE_SCORE];
    }

    /**
     *
     * @param int $entryIdentifiers []
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function retrieveEntriesByIdentifiers($entryIdentifiers)
    {
        $condition = new InCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID), $entryIdentifiers
        );

        return $this->dataClassRepository->retrieves(
            $this->getEntryClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     *
     * @param int $entityType
     * @param int $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $orderProperty
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function retrieveEntriesForEntityTypeAndId(
        $entityType, $entityId, Condition $condition = null, $offset = null, $count = null, $orderProperty = null
    )
    {
        $condition = $this->getEntityTypeAndIdCondition($entityType, $entityId, $condition);

        $joins = new Joins();

        $joins->add(
            new Join(
                User::class, new EqualityCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID),
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_USER_ID)
                )
            )
        );

        $joins->add(
            new Join(
                ContentObject::class, new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_CONTENT_OBJECT_ID)
                )
            )
        );

        $joins->add(
            new Join(
                $this->getScoreClassName(), new EqualityCondition(
                new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID),
                new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID)
            ), Join::TYPE_LEFT
            )
        );

        $properties = new RetrieveProperties();

        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));

        $properties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE));
        $properties->add(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED));
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID));
        $properties->add(new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_SCORE));
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_CONTENT_OBJECT_ID));
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_USER_ID));
        $properties->add(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_IP_ADDRESS));
        $properties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE));

        $parameters = new RecordRetrievesParameters($properties, $condition, $count, $offset, $orderProperty, $joins);

        return $this->dataClassRepository->records($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param int $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieveEntryByIdentifier($entryIdentifier)
    {
        return $this->dataClassRepository->retrieveById($this->getEntryClassName(), $entryIdentifier);
    }

    /**
     *
     * @param int $entityType
     * @param int $entityId
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    protected function retrieveLastScoreForEntityTypeAndId($entityType, $entityId, Condition $condition)
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getScoreClassName(), new EqualityCondition(
                    new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
                    new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID)
                )
            )
        );

        $properties = new RetrieveProperties();
        $properties->add(new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_SCORE));

        $parameters = new RecordRetrieveParameters(
            $properties, $this->getEntityTypeAndIdCondition($entityType, $entityId, $condition), new OrderBy([
            new OrderProperty(
                new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED), SORT_DESC
            )
        ]), $joins
        );

        $record = $this->dataClassRepository->record($this->getEntryClassName(), $parameters);

        return $record[Score::PROPERTY_SCORE];
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score
     */
    public function retrieveScoreByEntry(Entry $entry)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getScoreClassName(), Score::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );

        $score = $this->dataClassRepository->retrieve(
            $this->getScoreClassName(), new DataClassRetrieveParameters($condition)
        );

        if ($score instanceof Score)
        {
            return $score;
        }
        else
        {
            return null;
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return bool
     */
    public function updateEntry(Entry $entry)
    {
        return $this->dataClassRepository->update($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\EntryAttachment $entryAttachment
     *
     * @return bool
     */
    public function updateEntryAttachment(EntryAttachment $entryAttachment)
    {
        return $this->dataClassRepository->update($entryAttachment);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score $score
     *
     * @return bool
     */
    public function updateScore(Score $score)
    {
        return $this->dataClassRepository->update($score);
    }
}