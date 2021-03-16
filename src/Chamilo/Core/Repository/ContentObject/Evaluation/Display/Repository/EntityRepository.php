<?php

namespace Chamilo\Core\Repository\ContentObject\Evaluation\Display\Repository;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\FilterParametersTranslator;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\OrderBy;

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

    public function getUsersFromIds(array $userIds, $sortColumn = null, bool $sortDesc = false, $offset = null, $count = null)
    {
        $condition = new InCondition(new PropertyConditionVariable(User::class_name(), DataClass::PROPERTY_ID), $userIds);
        $sortProperties = [
            'firstname' => new PropertyConditionVariable(User::class, User::PROPERTY_FIRSTNAME),
            'lastname' => new PropertyConditionVariable(User::class, User::PROPERTY_LASTNAME),
            'official_code' => new PropertyConditionVariable(User::class, User::PROPERTY_OFFICIAL_CODE),
        ];
        if (array_key_exists($sortColumn, $sortProperties))
        {
            $orderBy = [new OrderBy($sortProperties[$sortColumn], $sortDesc ? SORT_DESC : SORT_ASC)];
        }
        $parameters = new RecordRetrievesParameters(null, $condition, $count, $offset, $orderBy);
        return $this->dataClassRepository->records(User::class_name(), $parameters);
    }

}