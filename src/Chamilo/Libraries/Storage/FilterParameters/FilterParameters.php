<?php

namespace Chamilo\Libraries\Storage\FilterParameters;

use Chamilo\Libraries\Storage\Query\OrderBy;

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
     * @param string|null $globalSearchQuery
     * @param int|null $offset
     * @param int|null $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderBy
     * @param \Chamilo\Libraries\Storage\FilterParameters\DataClassSearchQuery[] $dataClassSearchQueries
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
     * @param string|null $globalSearchQuery
     *
     * @return FilterParameters
     */
    public function setGlobalSearchQuery(string $globalSearchQuery = null)
    {
        $this->globalSearchQuery = $globalSearchQuery;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\Storage\FilterParameters\DataClassSearchQuery[]
     */
    public function getDataClassSearchQueries(): array
    {
        return $this->dataClassSearchQueries;
    }

    /**
     * @param \Chamilo\Libraries\Storage\FilterParameters\DataClassSearchQuery[] $dataClassSearchQueries
     *
     * @return FilterParameters
     */
    public function setDataClassSearchQueries(array $dataClassSearchQueries)
    {
        $this->dataClassSearchQueries = $dataClassSearchQueries;

        return $this;
    }

    /**
     * @param DataClassSearchQuery $dataClassSearchQuery
     *
     * @return $this
     */
    public function addDataClassSearchQuery(DataClassSearchQuery $dataClassSearchQuery)
    {
        $this->dataClassSearchQueries[] = $dataClassSearchQuery;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @param int|null $offset
     *
     * @return FilterParameters
     */
    public function setOffset(?int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int|null $count
     *
     * @return FilterParameters
     */
    public function setCount(?int $count)
    {
        $this->count = $count;

        return $this;
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
     *
     * @return FilterParameters
     */
    public function setOrderBy(array $orderBy = [])
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @param OrderBy $orderBy
     *
     * @return $this
     */
    public function addOrderBy(OrderBy $orderBy)
    {
        $this->orderBy[] = $orderBy;

        return $this;
    }
}
