<?php
namespace Chamilo\Libraries\Format\Table;

/**
 * This class represents a component for a table (this can be a cell renderer, a column model, a data provider...)
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class TableComponent
{

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    
    /**
     * The table in which this data provider is used
     * 
     * @var Table
     */
    private $table;

    /**
     * **************************************************************************************************************
     * Constructor *
     * **************************************************************************************************************
     */
    
    /**
     * Constructor
     * 
     * @param Table $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the table
     * 
     * @return Table
     */
    public function get_table()
    {
        return $this->table;
    }

    /**
     * Sets the table
     * 
     * @param Table $table
     */
    public function set_table($table)
    {
        $this->table = $table;
    }

    /**
     * **************************************************************************************************************
     * Delegation Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the component of the object table
     * 
     * @return mixed <Application, SubManager>
     */
    public function get_component()
    {
        return $this->get_table()->get_component();
    }
}
