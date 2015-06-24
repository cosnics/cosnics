<?php
namespace Chamilo\Core\Tracking\Table\Event;

use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * $Id: event_browser_table_column_model.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * 
 * @package tracking.lib.tracking_manager.component.admin_event_browser
 */
/**
 * Table column model for the user browser table
 */
class EventTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Event :: class_name(), Event :: PROPERTY_CONTEXT));
        $this->add_column(new DataClassPropertyTableColumn(Event :: class_name(), Event :: PROPERTY_NAME));
    }
}
