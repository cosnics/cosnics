<?php
namespace Chamilo\Libraries\Format\Table;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TableParameterValues extends AbstractBaseTableParameters
{
    public const PARAM_NUMBER_OF_COLUMNS_PER_PAGE = 'columns_per_page';
    public const PARAM_NUMBER_OF_ROWS_PER_PAGE = 'per_page';
    public const PARAM_OFFSET = 'offset';
    public const PARAM_SELECT_ALL = 'selectall';

    public function getNumberOfColumnsPerPage(): int
    {
        return $this->tableParameters[self::PARAM_NUMBER_OF_COLUMNS_PER_PAGE];
    }

    public function getNumberOfRowsPerPage(): int
    {
        return $this->tableParameters[self::PARAM_NUMBER_OF_ROWS_PER_PAGE];
    }

    public function getOffset(): int
    {
        return $this->tableParameters[self::PARAM_OFFSET];
    }

    public function getSelectAll(): int
    {
        return $this->tableParameters[self::PARAM_SELECT_ALL];
    }

    public function setNumberOfColumnsPerPage(int $numberOfColumnsPerPage): TableParameterValues
    {
        $this->tableParameters[self::PARAM_NUMBER_OF_COLUMNS_PER_PAGE] = $numberOfColumnsPerPage;

        return $this;
    }

    public function setNumberOfRowsPerPage(int $numberOfRowsPerPage): TableParameterValues
    {
        $this->tableParameters[self::PARAM_NUMBER_OF_ROWS_PER_PAGE] = $numberOfRowsPerPage;

        return $this;
    }

    public function setOffset(int $offset): TableParameterValues
    {
        $this->tableParameters[self::PARAM_OFFSET] = $offset;

        return $this;
    }

    public function setSelectAll(int $selectAll): TableParameterValues
    {
        $this->tableParameters[self::PARAM_SELECT_ALL] = $selectAll;

        return $this;
    }

}