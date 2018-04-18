<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssignmentRequestRepository extends CommonDataClassRepository
{
    /**
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     * @param string $entryClassName
     *
     * @return ContentObject[]
     */
    public function retrieveAssignmentEntriesWithRequests(
        RecordRetrievesParameters $recordRetrievesParameters, string $entryClassName
    )
    {
        $properties = new DataClassProperties();
        $properties->add(
            new FixedPropertyConditionVariable(Request::class, Request::PROPERTY_ID, Request::PROPERTY_REQUEST_ID)
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
        $properties->add(new PropertyConditionVariable(Entry::class, Entry::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(Entry::class, Entry::PROPERTY_ENTITY_TYPE));
        $properties->add(new PropertyConditionVariable(Entry::class, Entry::PROPERTY_SUBMITTED));
        $properties->add(new PropertyConditionVariable(Entry::class, Entry::PROPERTY_ID));
        $properties->add(new PropertyConditionVariable(Entry::class, Entry::PROPERTY_ID));
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
        foreach($records as $record)
        {
            $dataClasses[] =
                $this->dataClassRepository->getDataClassFactory()->getDataClass(ContentObject::class, $record);
        }

        return $dataClasses;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param string $entryClassName
     *
     * @return int
     */
    public function countAssignmentEntriesWithRequests(Condition $condition, string $entryClassName)
    {
        $dataClassCountParameters = new DataClassCountParameters($condition, $this->getAssignmentRequestJoins());
        return $this->dataClassRepository->count($entryClassName, $dataClassCountParameters);
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    protected function getAssignmentRequestJoins()
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                ContentObject::class,
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    new PropertyConditionVariable(Entry::class, Entry::PROPERTY_CONTENT_OBJECT_ID)
                )
            )
        );

        $joins->add(
            new Join(
                User::class,
                new EqualityCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID),
                    new PropertyConditionVariable(Entry::class, Entry::PROPERTY_USER_ID)
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
                            new PropertyConditionVariable(Entry::class, Entry::PROPERTY_CONTENT_OBJECT_ID)
                        ),
                        new EqualityCondition(
                            new PropertyConditionVariable(Request::class, Request::PROPERTY_AUTHOR_ID),
                            new PropertyConditionVariable(Entry::class, Entry::PROPERTY_USER_ID)
                        )
                    ]
                ),
                Join::TYPE_LEFT
            )
        );

        return $joins;
    }
}