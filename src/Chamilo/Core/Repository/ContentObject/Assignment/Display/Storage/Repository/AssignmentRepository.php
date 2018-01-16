<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableColumnModel;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
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
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return bool
     */
    public function createEntry(Entry $entry)
    {
        return $this->dataClassRepository->create($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return bool
     */
    public function updateEntry(Entry $entry)
    {
        return $this->dataClassRepository->update($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return bool
     */
    public function deleteEntry(Entry $entry)
    {
        return $this->dataClassRepository->delete($entry);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return bool
     */
    public function deleteFeedbackForEntry(Entry $entry)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getFeedbackClassName(), Feedback::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );

        return $this->dataClassRepository->deletes($this->getFeedbackClassName(), $condition);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score $score
     *
     * @return bool
     */
    public function createScore(Score $score)
    {
        return $this->dataClassRepository->create($score);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score $score
     *
     * @return bool
     */
    public function updateScore(Score $score)
    {
        return $this->dataClassRepository->update($score);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note $note
     *
     * @return bool
     */
    public function createNote(Note $note)
    {
        return $this->dataClassRepository->create($note);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note $note
     *
     * @return bool
     */
    public function updateNote(Note $note)
    {
        return $this->dataClassRepository->update($note);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function createFeedback(Feedback $feedback)
    {
        return $this->dataClassRepository->create($feedback);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function updateFeedback(Feedback $feedback)
    {
        return $this->dataClassRepository->update($feedback);
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return integer
     */
    public function countFeedbackByEntryIdentifier($entryIdentifier)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getFeedbackClassName(), Feedback::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entryIdentifier)
        );

        return $this->dataClassRepository->count(
            $this->getFeedbackClassName(), new DataClassCountParameters($condition)
        );
    }

    /**
     *
     * @param integer $entryIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieveEntryByIdentifier($entryIdentifier)
    {
        return $this->dataClassRepository->retrieveById($this->getEntryClassName(), $entryIdentifier);
    }

    /**
     *
     * @param integer[] $entryIdentifiers []
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function retrieveEntriesByIdentifiers($entryIdentifiers)
    {
        $condition = new InCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID),
            $entryIdentifiers
        );

        return $this->dataClassRepository->retrieves(
            $this->getEntryClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Note
     */
    public function retrieveNoteByEntry(Entry $entry)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getNoteClassName(), Note::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );

        $note = $this->dataClassRepository->retrieve(
            $this->getNoteClassName(), new DataClassRetrieveParameters($condition)
        );

        if ($note instanceof Note)
        {
            return $note;
        }
        else
        {
            return null;
        }
    }

    /**
     *
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Feedback|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieveFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->dataClassRepository->retrieveById($this->getFeedbackClassName(), $feedbackIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return integer
     */
    public function countFeedbackByEntry(Entry $entry)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getFeedbackClassName(), Feedback::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );

        return $this->dataClassRepository->count(
            $this->getFeedbackClassName(), new DataClassCountParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry $entry
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findFeedbackByEntry(Entry $entry)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable($this->getFeedbackClassName(), Feedback::PROPERTY_ENTRY_ID),
            new StaticConditionVariable($entry->getId())
        );

        return $this->dataClassRepository->retrieves(
            $this->getFeedbackClassName(), new DataClassRetrievesParameters($condition)
        );
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
     * @param Condition $condition
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $joinCondition
     * @param integer $offset
     * @param integer $count
     * @param OrderBy[] $orderBy
     * @param DataClassProperties $properties
     * @param string $baseClass
     * @param PropertyConditionVariable $baseVariable
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    protected function findTargetsForEntityType(
        $entityType, Condition $condition = null, Condition $joinCondition = null, $offset, $count, $orderBy,
        DataClassProperties $properties, $baseClass, $baseVariable
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
     * @param integer $entityType
     * @param integer $entityId
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    protected function countFeedbackByEntityTypeAndEntityId(
        $entityType, $entityId, Condition $condition = null
    )
    {
        $conditions = array();

        ($condition instanceof Condition) ? $conditions[] = $condition : null;

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($entityId)
        );

        $condition = new AndCondition($conditions);

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

        $parameters = new DataClassCountParameters($condition, $joins);

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param integer $entityType
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
            $this->getEntityTypeCondition($entityType, $condition),
            null,
            $property
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param integer $entityType
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    protected function countDistinctFeedbackByEntityType($entityType, Condition $condition = null)
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
            $this->getEntityTypeCondition($entityType, $condition),
            $joins,
            $property
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param integer $entityType
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

        $conditions = array();
        $conditions[] = $this->getEntityTypeCondition($entityType, $condition);

        $conditions[] = new ComparisonCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED),
            ComparisonCondition::GREATER_THAN,
            new StaticConditionVariable($assignment->get_end_time())
        );

        $condition = new AndCondition($conditions);
        $parameters = new DataClassCountParameters($condition, null, $property);

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param integer $entityType
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getEntityTypeCondition($entityType, Condition $condition = null)
    {
        $conditions = array();

        ($condition instanceof Condition) ? $conditions[] = $condition : null;

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($entityType)
        );

        return new AndCondition($conditions);
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return integer
     */
    protected function countEntriesByEntityTypeAndId($entityType, $entityId, Condition $condition = null)
    {
        $condition = $this->getEntityTypeAndIdCondition($entityType, $entityId, $condition);

        return $this->dataClassRepository->count($this->getEntryClassName(), new DataClassCountParameters($condition));
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    protected function countDistinctFeedbackForEntityTypeAndId($entityType, $entityId, Condition $condition = null)
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
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID)
        );

        $parameters = new DataClassCountParameters(
            $this->getEntityTypeAndIdCondition($entityType, $entityId, $condition),
            $joins,
            $property
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return int
     */
    protected function countDistinctScoreForEntityTypeAndId($entityType, $entityId, Condition $condition = null)
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

        $property = new FunctionConditionVariable(
            FunctionConditionVariable::DISTINCT,
            new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_ID)
        );

        $parameters = new DataClassCountParameters(
            $this->getEntityTypeAndIdCondition($entityType, $entityId, $condition),
            $joins,
            $property
        );

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
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
            $this->getEntityTypeAndIdCondition($entityType, $entityId, $condition),
            array(),
            $joins
        );

        $record = $this->dataClassRepository->record($this->getEntryClassName(), $parameters);

        return $record[AssignmentDataProvider::AVERAGE_SCORE];
    }

    /**
     *
     * @param integer $entityType
     * @param integer $entityId
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function retrieveEntriesForEntityTypeAndId(
        $entityType, $entityId, Condition $condition = null, $offset = null, $count = null, $orderProperty = []
    )
    {
        $condition = $this->getEntityTypeAndIdCondition($entityType, $entityId, $condition);

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
     * @param integer $entityType
     * @param integer[] $entityIdentifiers
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    protected function findEntriesByEntityTypeAndIdentifiers(
        $entityType, $entityIdentifiers, Condition $condition
    )
    {
        $conditions = array();

        ($condition instanceof Condition) ? $conditions[] = $condition : null;

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
            $condition,
            new OrderBy(new PropertyConditionVariable($this->getEntryClassName(), Entry::PROPERTY_SUBMITTED), SORT_DESC)
        );

        return $this->dataClassRepository->retrieve($this->getEntryClassName(), $retrieveParameters);
    }

    /**
     *
     * @param integer $entityType
     * @param $entityId
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    protected function getEntityTypeAndIdCondition($entityType, $entityId, Condition $condition = null)
    {
        $conditions = array();

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
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findEntries(Condition $condition)
    {
        return $this->dataClassRepository->retrieves(
            $this->getEntryClassName(), new DataClassRetrievesParameters($condition)
        );
    }

    /**
     * @return string
     */
    abstract protected function getEntryClassName();

    /**
     * @return string
     */
    abstract protected function getFeedbackClassName();

    /**
     * @return string
     */
    abstract protected function getNoteClassName();

    /**
     * @return string
     */
    abstract protected function getScoreClassName();
}