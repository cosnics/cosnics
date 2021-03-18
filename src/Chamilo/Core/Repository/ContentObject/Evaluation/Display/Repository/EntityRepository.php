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
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 * @package Chamilo\Core\Repository\ContentObject\Evaluation\Display\Repository
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
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

    /**
     *
     * @param int[] $userIds
     * @param FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function getUsersFromIDs(array $userIds, FilterParameters $filterParameters)
    {
        $class_name = User::class_name();
        $condition = new InCondition(new PropertyConditionVariable($class_name, DataClass::PROPERTY_ID), $userIds);

        $retrieveProperties = $searchProperties = $this->getDataClassProperties();
        $parameters = new RecordRetrievesParameters($retrieveProperties);

        $this->filterParametersTranslator->translateFilterParameters($filterParameters, $searchProperties, $parameters, $condition);

        return $this->dataClassRepository->records($class_name, $parameters);
    }

    /**
     *
     * @param int[] $userIds
     * @param FilterParameters $filterParameters
     *
     * @return integer
     */
    public function countUsersFromIDs(array $userIds, FilterParameters $filterParameters)
    {
        $class_name = User::class_name();
        $condition = new InCondition(new PropertyConditionVariable($class_name, DataClass::PROPERTY_ID), $userIds);

        $retrieveProperties = $searchProperties = $this->getDataClassProperties();
        $parameters = new DataClassCountParameters();
        $parameters->setDataClassProperties($retrieveProperties);

        $this->filterParametersTranslator->translateFilterParameters($filterParameters, $searchProperties, $parameters, $condition);

        return $this->dataClassRepository->count($class_name, $parameters);
    }

    /**
     * @return DataClassProperties
     */
    protected function getDataClassProperties(): DataClassProperties
    {
        $class_name = User::class_name();
        $properties = new DataClassProperties([
            new PropertyConditionVariable($class_name, User::PROPERTY_FIRSTNAME),
            new PropertyConditionVariable($class_name, User::PROPERTY_LASTNAME),
            new PropertyConditionVariable($class_name, User::PROPERTY_OFFICIAL_CODE)
        ]);
        return $properties;
    }


}