<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
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
abstract class FeedbackRepository
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function createFeedback(Feedback $feedback)
    {
        return $this->dataClassRepository->create($feedback);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function updateFeedback(Feedback $feedback)
    {
        return $this->dataClassRepository->update($feedback);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function deleteFeedback(Feedback $feedback)
    {
        return $this->dataClassRepository->delete($feedback);
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
     * @param integer $feedbackIdentifier
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Feedback|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function retrieveFeedbackByIdentifier($feedbackIdentifier)
    {
        return $this->dataClassRepository->retrieveById($this->getFeedbackClassName(), $feedbackIdentifier);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
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
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
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
     * @return string
     */
    abstract protected function getEntryClassName();

    /**
     * @return string
     */
    abstract protected function getFeedbackClassName();
}