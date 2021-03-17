<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\FilterParametersTranslator;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;

class EntityRepository
{
    /**
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     * @param \Chamilo\Libraries\Storage\Query\FilterParametersTranslator $filterParametersTranslator
     */
    public function __construct(
        DataClassRepository $dataClassRepository, FilterParametersTranslator $filterParametersTranslator
    )
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->filterParametersTranslator = $filterParametersTranslator;
    }

    public function getUsersFromIds(array $userIds, array $sortProperties, $sortColumn = null, bool $sortDesc = false, $offset = null, $count = null)
    {
        $condition = new InCondition(new PropertyConditionVariable(User::class_name(), DataClass::PROPERTY_ID), $userIds);

        $orderBy = array();

        if (array_key_exists($sortColumn, $sortProperties))
        {
            $orderBy[] = new OrderBy($sortProperties[$sortColumn], $sortDesc ? SORT_DESC : SORT_ASC);
        }

        $retrieveProperties = $searchProperties = $this->getRetrieveProperties();

        $filterParameters = new FilterParameters(null, $offset, $count, $orderBy);

        $parameters = new RecordRetrievesParameters($retrieveProperties);
        $this->filterParametersTranslator->translateFilterParameters($filterParameters, $searchProperties, $parameters, $condition);

        return $this->dataClassRepository->records(User::class_name(), $parameters);
    }

    /**
     * @return DataClassProperties
     */
    protected function getRetrieveProperties(): DataClassProperties
    {
        $retrieveProperties = new DataClassProperties([
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_FIRSTNAME),
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_LASTNAME),
            new PropertyConditionVariable(User::class_name(), User::PROPERTY_OFFICIAL_CODE)
        ]);
        return $retrieveProperties;
    }


}