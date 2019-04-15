<?php

namespace Chamilo\Libraries\Storage\Parameters;

/**
 * This class is used to communicate between the context (component, table, ajax, ...) and the repository of package.
 * This class transfers some generic parameters like search query,
 *
 * @package Chamilo\Libraries\Storage\Parameters
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class FilterParameters
{
    /**
     * @var string
     */
    protected $globalSearchQuery;

    /**
     * @var DataClassSearchQuery[]
     */
    protected $dataClassSearchQueries;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var \Chamilo\Libraries\Storage\Query\OrderBy[]
     */
    protected $orderBy;

    /**
     * @param string $globalSearchQuery
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassSearchQuery[] $dataClassSearchQueries
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     */
    public function __construct(
        string $globalSearchQuery = null, int $offset = null, int $count = null, array $orderBy = array(),
        array $dataClassSearchQueries = array()
    )
    {
        $this->globalSearchQuery = $globalSearchQuery;
        $this->dataClassSearchQueries = $dataClassSearchQueries;
        $this->offset = $offset;
        $this->count = $count;
        $this->orderBy = $orderBy;
    }

    /**
     * @return string
     */
    public function getGlobalSearchQuery(): ?string
    {
        return $this->globalSearchQuery;
    }

    /**
     * @param string $globalSearchQuery
     */
    public function setGlobalSearchQuery(string $globalSearchQuery = null): void
    {
        $this->globalSearchQuery = $globalSearchQuery;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Parameters\DataClassSearchQuery[]
     */
    public function getDataClassSearchQueries(): array
    {
        return $this->dataClassSearchQueries;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\DataClassSearchQuery[] $dataClassSearchQueries
     */
    public function setDataClassSearchQueries(array $dataClassSearchQueries): void
    {
        $this->dataClassSearchQueries = $dataClassSearchQueries;
    }

    /**
     * @return int
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return \Chamilo\Libraries\Storage\Query\OrderBy[]
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     */
    public function setOrderBy(array $orderBy): void
    {
        $this->orderBy = $orderBy;
    }
}