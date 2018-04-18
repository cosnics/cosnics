<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RequestRepository extends CommonDataClassRepository
{
    /**
     * Retrieves a request by a given guid
     *
     * @param string $guid
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass | Request
     */
    public function findRequestByGuid(string $guid)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_GUID),
            new StaticConditionVariable($guid));

        return $this->dataClassRepository->retrieve(Request::class, new DataClassRetrieveParameters($condition));
    }

    /**
     * Retrieves a request by a given id
     *
     * @param int $id
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass | Request
     */
    public function findRequestById(int $id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_ID),
            new StaticConditionVariable($id));

        return $this->dataClassRepository->retrieve(Request::class, new DataClassRetrieveParameters($condition));
    }
}