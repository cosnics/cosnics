<?php

namespace Chamilo\Libraries\Storage\Parameters;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * @package Chamilo\Libraries\Storage\Parameters
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop - Erasmus Hogeschool Brussel
 */
class DataClassTableParametersConverter
{

    /**
     * Parses a string based order property to an orderBy. The property string exists out of
     * FQCN (underscores instead of \ due to javascript issues) : propertyName.
     *
     * E.g Chamilo_Core_Repository_Storage_DataClass_ContentObject:title
     *
     * @param string $orderProperty
     * @param bool $isReverseOrder
     *
     * @return \Chamilo\Libraries\Storage\Query\OrderBy[]
     */
    public function convertOrderByProperty($orderProperty = null, $isReverseOrder = false)
    {
        if(empty($orderProperty))
        {
            return [];
        }

        return array(
            new OrderBy(
                $this->convertPropertyStringToPropertyConditionVariable($orderProperty),
                $isReverseOrder ? SORT_DESC : SORT_ASC
            )
        );
    }

    /**
     * @param string[] $individualFilters
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function convertIndividualFiltersIntoConditions($individualFilters = [])
    {
        if(empty($individualFilters))
        {
            return null;
        }

        $conditions = [];

        foreach ($individualFilters as $propertyString => $filterText)
        {
            $property = $this->convertPropertyStringToPropertyConditionVariable($propertyString);
            $conditions = array_merge($conditions, $this->createConditionsForSearchTerms($filterText, [$property]));
        }

        return new AndCondition($conditions);
    }

    /**
     * @param string $globalFilter
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[] $globalFilterProperties
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     *
     */
    public function convertGlobalFilterIntoCondition($globalFilter = null, $globalFilterProperties = [])
    {
        if(empty($globalFilter))
        {
            return null;
        }

        return new AndCondition($this->createConditionsForSearchTerms($globalFilter, $globalFilterProperties));
    }

    /**
     * Returns the filter parameters
     *
     * @param string $globalFilter
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[] $globalFilterProperties
     * @param string[] $individualFilters
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    protected function convertFiltersIntoConditions($globalFilter, $globalFilterProperties, $individualFilters)
    {
        $conditions = [];

        $globalFilterCondition = $this->convertIndividualFiltersIntoConditions($individualFilters);
        if($globalFilterCondition instanceof Condition)
        {
            $conditions[] = $globalFilterCondition;
        }

        $individualFiltersCondition = $this->convertGlobalFilterIntoCondition($globalFilter, $globalFilterProperties);
        if($individualFiltersCondition instanceof Condition)
        {
            $conditions[] = $individualFiltersCondition;
        }

        return new AndCondition($conditions);
    }

    /**
     * @param int $currentPage
     * @param int $itemsPerPage
     *
     * @return int
     */
    public function calculateOffset($currentPage = 0, $itemsPerPage = 20)
    {
        return $currentPage * $itemsPerPage;
    }

    /**
     * Dynamically builds the DataClassRetrievesParameters for the given parameters
     *
     * @param int $currentPage
     * @param int $itemsPerPage
     * @param string $globalFilter
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[] $globalFilterProperties
     * @param string[] $individualFilters
     * @param string $orderProperty
     * @param bool $isReverseOrder
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters
     */
    public function buildDataClassRetrievesParameters(
        $currentPage = 0, $itemsPerPage = 10, $globalFilter = null, $globalFilterProperties = [],
        $individualFilters = [],
        $orderProperty = null, $isReverseOrder = false
    )
    {
        return new DataClassRetrievesParameters(
            $this->convertFiltersIntoConditions($globalFilter, $globalFilterProperties, $individualFilters),
            $itemsPerPage, $this->calculateOffset($currentPage, $itemsPerPage),
            $this->convertOrderByProperty($orderProperty, $isReverseOrder)
        );
    }

    /**
     * Dynamically builds the DataClassRetrievesParameters for the given parameters
     *
     * @param int $currentPage
     * @param int $itemsPerPage
     * @param string $globalFilter
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[] $globalFilterProperties
     * @param string[] $individualFilters
     * @param string $orderProperty
     * @param bool $isReverseOrder
     *
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters
     */
    public function buildRecordRetrievesParameters(
        $currentPage = 0, $itemsPerPage = 10, $globalFilter = null, $globalFilterProperties = [],
        $individualFilters = [],
        $orderProperty = null, $isReverseOrder = false
    )
    {
        return new RecordRetrievesParameters(
            null, $this->convertFiltersIntoConditions($globalFilter, $globalFilterProperties, $individualFilters),
            $itemsPerPage, $this->calculateOffset($currentPage, $itemsPerPage),
            $this->convertOrderByProperty($orderProperty, $isReverseOrder)
        );
    }

    /**
     * Returns the relevant search terms
     *
     * @param string $filterText
     *
     * @return string[]
     */
    protected function getRelevantSearchTerms($filterText)
    {
        return explode(' ', $filterText);
    }

    /**
     * Creates new PatternMatch conditions for a given filter with the given data class properties,
     * defined by PropertyConditionVariable objects
     *
     * @param string $filterText
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[] $propertyConditionVariables
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition[]
     */
    protected function createConditionsForSearchTerms($filterText = '', $propertyConditionVariables = [])
    {
        $filterText = $this->getRelevantSearchTerms($filterText);
        $conditions = [];

        foreach ($propertyConditionVariables as $propertyConditionVariable)
        {
            foreach ($filterText as $searchTerm)
            {
                $conditions[] = new PatternMatchCondition($propertyConditionVariable, '*' . $searchTerm . '*');
            }
        }

        return $conditions;
    }

    /**
     * Converts a property string into a real PropertyConditionVariable
     *
     * The property string exists out of
     * FQCN (underscores instead of \ due to javascript issues) : propertyName.
     *
     * E.g Chamilo_Core_Repository_Storage_DataClass_ContentObject:title
     *
     * @param string $propertyString
     *
     * @return \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable
     */
    protected function convertPropertyStringToPropertyConditionVariable($propertyString)
    {
        $propertyStringParts = explode(':', $propertyString);

        if (empty($propertyStringParts) || count($propertyStringParts) != 2)
        {
            throw new \InvalidArgumentException(
                'The property string ' . $propertyString . ' is empty or could not be parsed'
            );
        }

        $propertyClassName = str_replace('_', '\\', $propertyStringParts[0]);

        if (!class_exists($propertyClassName) || !is_subclass_of($propertyClassName, DataClass::class))
        {
            throw new \InvalidArgumentException(
                'The class ' . $propertyClassName .
                ' is not a valid class or does not inherit from \Chamilo\Libraries\Storage\DataClass\DataClass'
            );
        }

        return new PropertyConditionVariable($propertyClassName, $propertyStringParts[1]);
    }
}