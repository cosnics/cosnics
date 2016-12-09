<?php
namespace Chamilo\Core\Rights\Editor\Table\LocationEntity;

use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\ObjectTableColumn;

abstract class LocationEntityTableColumnModel extends DataClassTableColumnModel
{

    /**
     * The table right columns
     */
    private static $rights_columns;

    public function initialize_columns()
    {
        $this->add_rights_columns();
    }

    /**
     * Determines wheter a column is a rights column
     * 
     * @param ObjectTableColumn $column
     * @return boolean
     */
    public static function is_rights_column($column)
    {
        return in_array($column, self::$rights_columns);
    }

    /**
     * Adds the rights columns to the column model
     */
    public function add_rights_columns()
    {
        $rights = $this->get_component()->get_available_rights();
        
        foreach ($rights as $right_name => $right_id)
        {
            $column = new StaticTableColumn($right_name);
            $this->add_column($column);
            
            self::$rights_columns[] = $column;
        }
    }
}
