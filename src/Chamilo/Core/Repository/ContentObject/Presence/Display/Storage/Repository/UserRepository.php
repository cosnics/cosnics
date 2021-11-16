<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultEntry;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultPeriod;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\FilterParametersTranslator;
use Chamilo\Libraries\Storage\Query\GroupBy;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
use Chamilo\Libraries\Storage\Query\Variable\FixedPropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Storage\Repository
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class UserRepository
{
    /**
     * @var DataClassRepository
     */
    private $dataClassRepository;

    /**
     * @var FilterParametersTranslator
     */
    private $filterParametersTranslator;

    /**
     *
     * @param DataClassRepository $dataClassRepository
     * @param FilterParametersTranslator $filterParametersTranslator
     */
    public function __construct(
        DataClassRepository $dataClassRepository, FilterParametersTranslator $filterParametersTranslator
    )
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->filterParametersTranslator = $filterParametersTranslator;
    }

    /**
     * @return DataClassProperties
     */
    protected function getRetrieveProperties(DataClassProperties $properties): DataClassProperties
    {
        $retrieveProperties = $properties->get();
        $retrieveProperties[] = new FixedPropertyConditionVariable(PresenceResultEntry::class_name(), PresenceResultEntry::PROPERTY_CHOICE_ID, 'status');
        $retrieveProperties[] = new PropertyConditionVariable(PresenceResultEntry::class_name(), PresenceResultEntry::PROPERTY_CHECKED_IN_DATE);
        $retrieveProperties[] = new PropertyConditionVariable(PresenceResultEntry::class_name(), PresenceResultEntry::PROPERTY_CHECKED_OUT_DATE);

        $retrieveProperties[] = new FixedPropertyConditionVariable(PresenceResultPeriod::class_name(), PresenceResultPeriod::PROPERTY_ID, 'period_id');

        return new DataClassProperties($retrieveProperties);
    }

    /**
     * @param $class
     * @param ContextIdentifier $contextIdentifier
     * @return Joins
     */
    public function getPresenceResultEntryJoins($class, ContextIdentifier $contextIdentifier): Joins
    {
        $joins = new Joins();

        $entryJoinConditions = array();
        $entryJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(PresenceResultEntry::class_name(), PresenceResultEntry::PROPERTY_USER_ID),
            new PropertyConditionVariable($class::class_name(), $class::PROPERTY_ID)
        );
        $entryJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(PresenceResultEntry::class_name(), PresenceResultEntry::PROPERTY_PRESENCE_PERIOD_ID),
            new PropertyConditionVariable(PresenceResultPeriod::class_name(), DataClass::PROPERTY_ID)
        );

        $periodJoinConditions = array();
        $periodJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(PresenceResultPeriod::class_name(), PresenceResultPeriod::PROPERTY_CONTEXT_CLASS),
            new StaticConditionVariable($contextIdentifier->getContextClass())
        );
        $periodJoinConditions[] = new EqualityCondition(
            new PropertyConditionVariable(PresenceResultPeriod::class_name(), PresenceResultPeriod::PROPERTY_CONTEXT_ID),
            new StaticConditionVariable($contextIdentifier->getContextId())
        );

        $joins->add(new Join(PresenceResultPeriod::class_name(), new AndCondition($periodJoinConditions), Join::TYPE_LEFT));
        $joins->add(new Join(PresenceResultEntry::class_name(), new AndCondition($entryJoinConditions), Join::TYPE_LEFT));

        return $joins;
    }

    /**
     * @return DataClassProperties
     */
    protected function getUserEntityDataClassProperties(): DataClassProperties
    {
        $class_name = User::class_name();
        return new DataClassProperties([
            new PropertyConditionVariable($class_name, User::PROPERTY_ID),
            new PropertyConditionVariable($class_name, User::PROPERTY_FIRSTNAME),
            new PropertyConditionVariable($class_name, User::PROPERTY_LASTNAME),
            new PropertyConditionVariable($class_name, User::PROPERTY_OFFICIAL_CODE)
        ]);
    }

    /**
     *
     * @param int[] $userIds
     * @param ContextIdentifier $contextIdentifier
     * @param FilterParameters $filterParameters
     * @param Condition|null $condition
     *
     * @return RecordIterator
     */
    public function getUsersFromIds(array $userIds, ContextIdentifier $contextIdentifier, FilterParameters $filterParameters, Condition $condition = null): RecordIterator
    {
        $usersCondition = new InCondition(new PropertyConditionVariable(User::class_name(), DataClass::PROPERTY_ID), $userIds);
        $condition = empty($condition) ? $usersCondition : new AndCondition([$usersCondition, $condition]);

        $searchProperties = $this->getUserEntityDataClassProperties();
        $retrieveProperties = $this->getRetrieveProperties($this->getUserEntityDataClassProperties());

        $joins = $this->getPresenceResultEntryJoins(User::class, $contextIdentifier);

        //$group_by = new GroupBy();
        //$group_by->add(new PropertyConditionVariable(User::class_name(), User::PROPERTY_ID));

        $parameters = new RecordRetrievesParameters($retrieveProperties);
        $parameters->setJoins($joins);
        //$parameters->setGroupBy($group_by);

        $this->filterParametersTranslator->translateFilterParameters($filterParameters, $searchProperties, $parameters, $condition);

        return $this->dataClassRepository->records(User::class_name(), $parameters);
    }
}