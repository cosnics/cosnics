<?php
namespace Chamilo\Libraries\Format\Table;

/**
 * This class represents a component for a table (this can be a cell renderer, a column model, a data provider...)
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableComponent
{

    /**
     * The table in which this data provider is used
     *
     * @var \Chamilo\Libraries\Format\Table\Table
     */
    private $table;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Format\Table\Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * Returns the component of the object table
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function get_component()
    {
        return $this->get_table()->get_component();
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Table\Table
     */
    public function get_table()
    {
        return $this->table;
    }

    /**
     *
     * @param \Chamilo\Libraries\Format\Table\Table $table
     */
    public function set_table(Table $table)
    {
        $this->table = $table;
    }
}
