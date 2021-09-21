<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultPeriod;
use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultEntry;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
use Chamilo\Libraries\Storage\Parameters\RecordRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Query\FilterParametersTranslator;
use Chamilo\Libraries\Storage\FilterParameters\FilterParameters;

/**
 * @package Chamilo\Core\Repository\ContentObject\Presence\Display\Storage\Repository
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PresenceRepository
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
    public function __construct(DataClassRepository $dataClassRepository, FilterParametersTranslator $filterParametersTranslator)
    {
        $this->dataClassRepository = $dataClassRepository;
        $this->filterParametersTranslator = $filterParametersTranslator;
    }

    /**
     * @param int $presenceId
     * @param ContextIdentifier $contextIdentifier
     *
     * @return RecordIterator
     */
    public function getResultPeriodsForPresence(int $presenceId, ContextIdentifier $contextIdentifier): RecordIterator
    {
        $class_name = PresenceResultPeriod::class_name();

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_PRESENCE_ID),
            new StaticConditionVariable($presenceId));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_CONTEXT_CLASS),
            new StaticConditionVariable($contextIdentifier->getContextClass()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_CONTEXT_ID),
            new StaticConditionVariable($contextIdentifier->getContextId()));

        $retrieveProperties = new DataClassProperties([
            new PropertyConditionVariable($class_name, DataClass::PROPERTY_ID),
            new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_LABEL),
            new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_DATE),
        ]);

        $orderBy = new OrderBy(new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_DATE));

        $parameters = new RecordRetrievesParameters($retrieveProperties);
        $parameters->setOrderBy($orderBy);

        $this->filterParametersTranslator->translateFilterParameters(new FilterParameters(), new DataClassProperties(), $parameters, new AndCondition($conditions));

        return $this->dataClassRepository->records($class_name, $parameters);
    }

    /**
     * @param int $presenceId
     * @param int $presenceResultPeriodId
     * @param ContextIdentifier $contextIdentifier
     * @return CompositeDataClass|DataClass|false
     */
    public function findResultPeriodForPresence(int $presenceId, int $presenceResultPeriodId, ContextIdentifier $contextIdentifier)
    {
        $class_name = PresenceResultPeriod::class_name();

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, DataClass::PROPERTY_ID),
            new StaticConditionVariable($presenceResultPeriodId));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_PRESENCE_ID),
            new StaticConditionVariable($presenceId));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_CONTEXT_CLASS),
            new StaticConditionVariable($contextIdentifier->getContextClass()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_CONTEXT_ID),
            new StaticConditionVariable($contextIdentifier->getContextId()));
        $retrieveProperties = new DataClassProperties([
            new PropertyConditionVariable($class_name, DataClass::PROPERTY_ID),
        ]);

        $parameters = new RecordRetrieveParameters($retrieveProperties, new AndCondition($conditions));;
        return $this->dataClassRepository->retrieve($class_name, $parameters);
    }

    /**
     * @param int $presencePeriodId
     * @param int $userId
     * @return CompositeDataClass|DataClass|false
     */
    public function getPresenceResultEntry(int $presencePeriodId, int $userId)
    {
        $class_name = PresenceResultEntry::class_name();
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, PresenceResultEntry::PROPERTY_PRESENCE_PERIOD_ID),
            new StaticConditionVariable($presencePeriodId));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable($class_name, PresenceResultEntry::PROPERTY_USER_ID),
            new StaticConditionVariable($userId));
        $parameters = new RecordRetrieveParameters(new DataClassProperties(), new AndCondition($conditions));
        return $this->dataClassRepository->retrieve($class_name, $parameters);
    }

    /**
     * @param PresenceResultEntry $presenceResultEntry
     *
     * @return bool
     */
    public function createPresenceResultEntry(PresenceResultEntry $presenceResultEntry): bool
    {
        return $this->dataClassRepository->create($presenceResultEntry);
    }

    /**
     * @param PresenceResultEntry $presenceResultEntry
     *
     * @return bool
     */
    public function updatePresenceResultEntry(PresenceResultEntry $presenceResultEntry): bool
    {
        return $this->dataClassRepository->update($presenceResultEntry);
    }

    /**
     * @param PresenceResultPeriod $presenceResultPeriod
     *
     * @return bool
     */
    public function createPresenceResultPeriod(PresenceResultPeriod $presenceResultPeriod): bool
    {
        return $this->dataClassRepository->create($presenceResultPeriod);
    }

    /**
     * @param PresenceResultPeriod $presenceResultPeriod
     *
     * @return bool
     */
    public function updatePresenceResultPeriod(PresenceResultPeriod $presenceResultPeriod): bool
    {
        return $this->dataClassRepository->update($presenceResultPeriod);
    }

    /**
     * @param PresenceResultPeriod $presenceResultPeriod
     *
     * @return bool
     */
    public function deletePresenceResultPeriod(PresenceResultPeriod $presenceResultPeriod): bool
    {
        return $this->dataClassRepository->delete($presenceResultPeriod);
    }
}
