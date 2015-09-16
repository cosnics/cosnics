<?php
namespace Chamilo\Libraries\Format\Table;

/**
 * Sortable table which can be used for data available in an array
 */
class SortableTableFromArray extends SortableTable
{

    /**
     * The array containing all data for this table
     * 
     * @var multitype:multitype
     */
    private $table_data;

    /**
     * Constructor
     * 
     * @param $table_data array
     * @param $default_column int
     * @param $default_items_per_page int
     */
    public function __construct($table_data, $default_column = 1, $default_items_per_page = 20, $tablename = 'tablename', 
        $default_direction = SORT_ASC)
    {
        $this->table_data = $table_data;
        parent :: __construct(
            $tablename, 
            array($this, 'get_total_number_of_items'), 
            array($this, 'get_table_data'), 
            $default_column, 
            $default_items_per_page, 
            $default_direction);
    }

    /**
     * Get table data to show on current page
     * 
     * @see SortableTable#get_table_data
     */
    public function get_table_data($from = 1)
    {
        $content = TableSort :: sort_table($this->table_data, $this->get_column(), $this->get_direction());
        return array_slice($content, $from, $this->get_per_page());
    }

    /**
     *
     * @return multitype:multitype
     */
    public function get_data()
    {
        return $this->table_data;
    }

    /**
     *
     * @param $table_data multitype:multitype
     */
    public function set_data($table_data)
    {
        $this->table_data = $table_data;
    }

    /**
     *
     * @param $data_row multitype:mixed
     */
    public function add_data($data_row)
    {
        $this->table_data[] = $data_row;
    }

    /**
     * Get total number of items
     * 
     * @see SortableTable#get_total_number_of_items
     */
    public function get_total_number_of_items()
    {
        return count($this->table_data);
    }
}
