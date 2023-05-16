<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Result;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RequestRepository extends CommonDataClassRepository
{
    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countRequestsWithContentObjects(Condition $condition)
    {
        return $this->dataClassRepository->count(
            Request::class, new DataClassCountParameters($condition, $this->getRequestJoins())
        );
    }

    /**
     * Retrieves a request by a given guid
     *
     * @param string $guid
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass |
     *     Request
     */
    public function findRequestByGuid(string $guid)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_GUID), new StaticConditionVariable($guid)
        );

        return $this->dataClassRepository->retrieve(Request::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * Retrieves a request by a given id
     *
     * @param int $id
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass |
     *     Request
     */
    public function findRequestById(int $id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_ID), new StaticConditionVariable($id)
        );

        return $this->dataClassRepository->retrieve(Request::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findRequestsWithContentObjects(RecordRetrievesParameters $recordRetrievesParameters)
    {
        $properties = new RetrieveProperties();

        $properties->add(new PropertiesConditionVariable(Request::class));
        $properties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE));
        $properties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION));
        $properties->add(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME));
        $properties->add(new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME));

        $joins = $this->getRequestJoins();

        $recordRetrievesParameters->setJoins($joins);
        $recordRetrievesParameters->setRetrieveProperties($properties);

        $records = $this->dataClassRepository->records(Request::class, $recordRetrievesParameters);

        $dataClasses = [];
        foreach ($records as $record)
        {
            $dataClasses[] =
                $this->dataClassRepository->getDataClassFactory()->getDataClass(ContentObject::class, $record);
        }

        return new ArrayCollection($dataClasses);
    }

    /**
     * @param int[] $guids
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findRequestsWithContentObjectsByGuids(array $guids = [])
    {
        $condition = new InCondition(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_GUID), $guids
        );

        $properties = new RetrieveProperties();

        $properties->add(new PropertiesConditionVariable(ContentObject::class));
        $properties->add(new PropertyConditionVariable(Request::class, Request::PROPERTY_GUID));

        $recordRetrievesParameters =
            new RecordRetrievesParameters($properties, $condition, null, null, null, $this->getRequestJoins());

        $records = $this->dataClassRepository->records(Request::class, $recordRetrievesParameters);

        $dataClasses = [];
        foreach ($records as $record)
        {
            $dataClasses[] =
                $this->dataClassRepository->getDataClassFactory()->getDataClass(ContentObject::class, $record);
        }

        return new ArrayCollection($dataClasses);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request $request
     *
     * @return Result[] | \Doctrine\Common\Collections\ArrayCollection
     */
    public function findResultsForRequest(Request $request)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Result::class, Result::PROPERTY_REQUEST_ID),
            new StaticConditionVariable($request->getId())
        );

        $orderBy = [new OrderProperty(new PropertyConditionVariable(Result::class, Result::PROPERTY_PERCENTAGE))];

        return $this->dataClassRepository->retrieves(
            Result::class, new DataClassRetrievesParameters($condition, null, null, new OrderBy($orderBy))
        );
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Joins
     */
    protected function getRequestJoins()
    {
        $joins = new Joins();

        $joins->add(
            new Join(
                ContentObject::class, new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_CONTENT_OBJECT_ID)
                )
            )
        );

        $joins->add(
            new Join(
                User::class, new EqualityCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID),
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_AUTHOR_ID)
                )
            )
        );

        return $joins;
    }
}