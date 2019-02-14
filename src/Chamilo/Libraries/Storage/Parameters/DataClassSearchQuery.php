<?php

namespace Chamilo\Libraries\Storage\Parameters;

/**
 * @package Chamilo\Libraries\Storage\Parameters
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DataClassSearchQuery
{
    /**
     * @var \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    protected $conditionVariable;

    /**
     * @var string
     */
    protected $searchQuery;

    /**
     * DataClassSearchQuery constructor.
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     * @param string $searchQuery
     */
    public function __construct(
        \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable, string $searchQuery
    )
    {
        $this->conditionVariable = $conditionVariable;
        $this->searchQuery = $searchQuery;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
     */
    public function getConditionVariable(): \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable
    {
        return $this->conditionVariable;
    }

    /**
     * @return string
     */
    public function getSearchQuery(): string
    {
        return $this->searchQuery;
    }

}