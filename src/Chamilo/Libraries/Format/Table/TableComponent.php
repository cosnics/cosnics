<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * This class represents a component for a table (this can be a cell renderer, a column model, a data provider...)
 *
 * @package Chamilo\Libraries\Format\Table
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableComponent
{

    private Table $table;

    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function setTable(Table $table)
    {
        $this->table = $table;
    }

    public function get_component(): Application
    {
        return $this->getTable()->get_component();
    }
}
