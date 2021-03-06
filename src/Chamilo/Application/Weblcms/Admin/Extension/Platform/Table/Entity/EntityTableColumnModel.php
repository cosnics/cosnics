<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Table\Entity;

use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * Table column model for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntityTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $helper_class = $this->get_table()->get_helper_class_name();
        
        foreach ($helper_class::get_table_columns() as $column)
        {
            $this->add_column($column);
        }
    }
}