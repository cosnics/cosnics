<?php

namespace Chamilo\Core\Repository\ContentObject\Presence\Display\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\PresenceResultPeriod;
use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;
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
            new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_ID),
            new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_LABEL),
            new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_DATE),
        ]);

        $orderBy = new OrderBy(new PropertyConditionVariable($class_name, PresenceResultPeriod::PROPERTY_DATE));

        $parameters = new RecordRetrievesParameters($retrieveProperties);
        $parameters->setOrderBy($orderBy);

        $this->filterParametersTranslator->translateFilterParameters(new FilterParameters(), new DataClassProperties(), $parameters, new AndCondition($conditions));

        return $this->dataClassRepository->records($class_name, $parameters);
    }
}
