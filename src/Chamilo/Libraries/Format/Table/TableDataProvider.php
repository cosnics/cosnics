<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

/**
 * This class represents a data provider for a table
 * Refactoring from ObjectTable to split between a table based on a record and based on an object
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableDataProvider extends TableComponent
{

    abstract public function countData(?Condition $condition = null): int;

    abstract public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    );
}
