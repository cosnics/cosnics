<?php
namespace Chamilo\Core\Admin\Table\WhoisOnline;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;

/**
 * $Id: whois_online_table_column_model.class.php 166 2009-11-12 11:03:06Z vanpouckesven $
 * 
 * @package admin.lib.admin_manager.component.whois_online_table
 */
/**
 * Table column model for the user browser table
 */
class WhoisOnlineTableColumnModel extends DataClassTableColumnModel
{

    /**
     * Constructor
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_EMAIL));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_STATUS));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_PICTURE_URI));
    }
}
