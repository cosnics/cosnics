<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Storage\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Ephorus\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class AssignmentEphorusRepository extends CommonDataClassRepository
{
    /**
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return ContentObject[] | DataClassIterator
     */
    public function findAssignmentEntriesWithRequests(RecordRetrievesParameters $recordRetrievesParameters = null)
    {
        $entryClassName = $this->getEntryClassName();

        $properties = new DataClassProperties();

        $properties->add(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_ID, Request::PROPERTY_REQUEST_ID)
        );

        $properties->add(new PropertyConditionVariable(Request::class, Request::PROPERTY_AUTHOR_ID));
        $properties->add(new PropertyConditionVariable(Request::class, Request::PROPERTY_CONTENT_OBJECT_ID));
        $properties->add(new PropertyConditionVariable(Request::class, Request::PROPERTY_COURSE_ID));
        $properties->add(new PropertyConditionVariable(Request::class, Request::PROPERTY_GUID));
        $properties->add(new PropertyConditionVariable(Request::class, Request::PROPERTY_PERCENTAGE));
        $properties->add(new PropertyConditionVariable(Request::class, Request::PROPERTY_PROCESS_TYPE));
        $properties->add(new PropertyConditionVariable(Request::class, Request::PROPERTY_REQUEST_TIME));
        $properties->add(new PropertyConditionVariable(Request::class, Request::PROPERTY_STATUS));
        $properties->add(new PropertyConditionVariable(Request::class, Request::PROPERTY_STATUS_DESCRIPTION));
        $properties->add(new PropertyConditionVariable(Request::class, Request::PROPERTY_VISIBLE_IN_INDEX));
        $properties->add(new PropertyConditionVariable($entryClassName, Entry::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable($entryClassName, Entry::PROPERTY_ENTITY_TYPE));
        $properties->add(new PropertyConditionVariable($entryClassName, Entry::PROPERTY_SUBMITTED));
        $properties->add(new PropertyConditionVariable($entryClassName, Entry::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable($entryClassName, Entry::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE));
        $properties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION));
        $properties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));

        $joins = $this->getAssignmentRequestJoins();

        $recordRetrievesParameters->setJoins($joins);
        $recordRetrievesParameters->setDataClassProperties($properties);

        $records = $this->dataClassRepository->records($entryClassName, $recordRetrievesParameters);

        $dataClasses = [];
        foreach ($records as $record)
        {
            $dataClasses[] =
                $this->dataClassRepository->getDataClassFactory()->getDataClass(ContentObject::class, $record);
        }

        return new DataClassIterator(ContentObject::class, $dataClasses);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countAssignmentEntriesWithRequests(Condition $condition = null)
    {
        $entryClassName = $this->getEntryClassName();

        return $this->dataClassRepository->count(
            $entryClassName, new DataClassCountParameters($condition, $this->getAssignmentRequestJoins())
        );
    }

    /**
     * @param int[] $entryIds
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator|Request[]
     */
    public function findEphorusRequestsForAssignmentEntries(array $entryIds = [], Condition $condition = null)
    {
        $entryClassName = $this->getEntryClassName();

        $conditions = [];

        if ($condition instanceof Condition)
        {
            $conditions[] = $condition;
        }

        $conditions[] =
            new InCondition(new PropertyConditionVariable($entryClassName, Entry::PROPERTY_ID), $entryIds);

        $condition = new AndCondition($conditions);

        $joins = new Joins();

        $joins->add(
            new Join(
                $this->getEntryClassName(),
                new AndCondition(
                    [
                        new EqualityCondition(
                            new PropertyConditionVariable(Request::class, Request::PROPERTY_CONTENT_OBJECT_ID),
                            new PropertyConditionVariable($entryClassName, Entry::PROPERTY_CONTENT_OBJECT_ID)
                        ),
                        new EqualityCondition(
                            new PropertyConditionVariable(Request::class, Request::PROPERTY_AUTHOR_ID),
                            new PropertyConditionVariable($entryClassName, Entry::PROPERTY_USER_ID)
                        )
                    ]
                )
            )
        );

        return $this->dataClassRepository->retrieves(
            Request::class, new DataClassRetrievesParameters($condition, null, null, null, $joins)
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    protected function getAssignmentRequestJoins()
    {
        $entryClassName = $this->getEntryClassName();

        $joins = new Joins();

        $joins->add(
            new Join(
                ContentObject::class,
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    new PropertyConditionVariable($entryClassName, Entry::PROPERTY_CONTENT_OBJECT_ID)
                )
            )
        );

        $joins->add(
            new Join(
                User::class,
                new EqualityCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID),
                    new PropertyConditionVariable($entryClassName, Entry::PROPERTY_USER_ID)
                )
            )
        );

        $joins->add(
            new Join(
                Request::class,
                new AndCondition(
                    [
                        new EqualityCondition(
                            new PropertyConditionVariable(Request::class, Request::PROPERTY_CONTENT_OBJECT_ID),
                            new PropertyConditionVariable($entryClassName, Entry::PROPERTY_CONTENT_OBJECT_ID)
                        ),
                        new EqualityCondition(
                            new PropertyConditionVariable(Request::class, Request::PROPERTY_AUTHOR_ID),
                            new PropertyConditionVariable($entryClassName, Entry::PROPERTY_USER_ID)
                        )
                    ]
                ),
                Join::TYPE_LEFT
            )
        );

        return $joins;
    }

    /**
     * @return string
     */
    abstract protected function getEntryClassName();
}