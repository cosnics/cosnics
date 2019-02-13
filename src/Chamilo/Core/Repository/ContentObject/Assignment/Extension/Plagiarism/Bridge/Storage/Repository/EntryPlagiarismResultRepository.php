<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class EntryPlagiarismResultRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * EntryPlagiarismResultRepository constructor.
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(
        \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
    )
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findEntryPlagiarismResultByEntry(Entry $entry)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getEntryPlagiarismResultClass(), EntryPlagiarismResult::PROPERTY_ENTRY_ID
            ), new StaticConditionVariable($entry->getId())
        );

        return $this->dataClassRepository->retrieve(
            $this->getEntryPlagiarismResultClass(), new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param string $externalId
     *
     * @return EntryPlagiarismResult|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findEntryPlagiarismResultByExternalId(string $externalId)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getEntryPlagiarismResultClass(), EntryPlagiarismResult::PROPERTY_EXTERNAL_ID
            ), new StaticConditionVariable($externalId)
        );

        return $this->dataClassRepository->retrieve(
            $this->getEntryPlagiarismResultClass(), new DataClassRetrieveParameters($condition)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult $entryPlagiarismResult
     *
     * @return bool
     */
    public function createEntryPlagiarismResult(EntryPlagiarismResult $entryPlagiarismResult)
    {
        return $this->dataClassRepository->create($entryPlagiarismResult);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult $entryPlagiarismResult
     *
     * @return bool
     */
    public function updateEntryPlagiarismResult(EntryPlagiarismResult $entryPlagiarismResult)
    {
        return $this->dataClassRepository->update($entryPlagiarismResult);
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
     * @param integer $entityType
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
    protected function findEntriesWithPlagiarismResult(
        $entityType, DataClassProperties $properties, $baseClass, $baseVariable,
        Condition $condition = null, $offset = null, $count = null, $orderBy = array()
    )
    {
        $properties->add(new PropertiesConditionVariable($this->getEntryClassName()));

        $properties->add(
            new PropertyConditionVariable(
                $this->getScoreClassName(),
                \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score::PROPERTY_SCORE
            )
        );

        $properties->add(
            new PropertyConditionVariable(
                $this->getEntryPlagiarismResultClass(),
                \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult::PROPERTY_RESULT
            )
        );

        $properties->add(
            new PropertyConditionVariable(
                $this->getEntryPlagiarismResultClass(),
                \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult::PROPERTY_EXTERNAL_ID
            )
        );

        $properties->add(
            new PropertyConditionVariable(
                $this->getEntryPlagiarismResultClass(),
                \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult::PROPERTY_ERROR
            )
        );

        $properties->add(
            new PropertyConditionVariable(
                $this->getEntryPlagiarismResultClass(),
                \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult::PROPERTY_STATUS
            )
        );

        $properties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE));
        $properties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION));

        $joins = $this->getEntryPlagiarismResultJoins($baseClass, $baseVariable);

        $joins->add(
            new Join(
                $this->getScoreClassName(), new EqualityCondition(
                new PropertyConditionVariable(
                    $this->getEntryClassName(),
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry::PROPERTY_ID
                ),
                new PropertyConditionVariable(
                    $this->getScoreClassName(),
                    \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Score::PROPERTY_ENTRY_ID
                )
            ),
                Join::TYPE_LEFT
            )
        );

        $condition = $this->getEntityTypeCondition($entityType, $condition);

        $parameters = new RecordRetrievesParameters(
            $properties,
            $condition,
            $count,
            $offset,
            $orderBy,
            $joins
        );

        return $this->dataClassRepository->records($this->getEntryClassName(), $parameters);
    }

    /**
     * @param integer $entityType
     * @param string $baseClass
     * @param PropertyConditionVariable $baseVariable
     * @param Condition $condition
     *
     * @return int
     */
    protected function countEntriesWithPlagiarismResult(
        $entityType, $baseClass, $baseVariable, Condition $condition = null
    )
    {
        $joins = $this->getEntryPlagiarismResultJoins($baseClass, $baseVariable);

        $condition = $this->getEntityTypeCondition($entityType, $condition);
        $parameters = new DataClassCountParameters($condition, $joins);

        return $this->dataClassRepository->count($this->getEntryClassName(), $parameters);
    }

    /**
     * @param int $entityType
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition|\Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function getEntityTypeCondition($entityType, Condition $condition)
    {
        $conditions = [];

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                $this->getEntryClassName(),
                \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry::PROPERTY_ENTITY_TYPE
            ),
            new StaticConditionVariable($entityType)
        );

        $condition = new AndCondition($conditions);

        return $condition;
    }

    /**
     * @param string $baseClass
     * @param string $baseVariable
     *
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    protected function getEntryPlagiarismResultJoins($baseClass, $baseVariable): \Chamilo\Libraries\Storage\Query\Joins
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getEntryPlagiarismResultClass(), new EqualityCondition(
                    new PropertyConditionVariable(
                        $this->getEntryClassName(),
                        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry::PROPERTY_ID
                    ),
                    new PropertyConditionVariable(
                        $this->getEntryPlagiarismResultClass(),
                        \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult::PROPERTY_ENTRY_ID
                    )
                )
            )
        );

        $joins->add(
            new Join(
                $baseClass, new EqualityCondition(
                    $baseVariable,
                    new PropertyConditionVariable(
                        $this->getEntryClassName(),
                        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry::PROPERTY_ENTITY_ID
                    )
                )
            )
        );

        $joins->add(
            new Join(
                ContentObject::class, new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    new PropertyConditionVariable(
                        $this->getEntryClassName(),
                        \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry::PROPERTY_CONTENT_OBJECT_ID
                    )
                )
            )
        );

        return $joins;
    }

    /**
     * @return string
     */
    abstract protected function getEntryClassName();

    /**
     * @return string
     */
    abstract protected function getScoreClassName();

    /**
     * @return string
     */
    abstract protected function getEntryPlagiarismResultClass();
}
