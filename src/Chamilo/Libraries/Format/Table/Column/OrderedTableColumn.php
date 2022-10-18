<?php
namespace Chamilo\Libraries\Format\Table\Column;

/**
 * @package Chamilo\Libraries\Format\Table\Column
 * @author  Scaramanga
 */
class OrderedTableColumn
{
    private int $orderDirection;

    private TableColumn $tableColumn;

    public function __construct(TableColumn $tableColumn, int $orderDirection = SORT_ASC)
    {
        $this->tableColumn = $tableColumn;
        $this->orderDirection = $orderDirection;
    }

    public function getOrderDirection(): int
    {
        return $this->orderDirection;
    }

    public function setOrderDirection(int $orderDirection): OrderedTableColumn
    {
        $this->orderDirection = $orderDirection;

        return $this;
    }

    public function getTableColumn(): TableColumn
    {
        return $this->tableColumn;
    }

    public function setTableColumn(TableColumn $tableColumn): OrderedTableColumn
    {
        $this->tableColumn = $tableColumn;

        return $this;
    }

}