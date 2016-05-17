<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Subscribed;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * $Id: course_group_subscribed_user_browser_table_column_model.class.php 216
 * 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class SubscribedUserTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_USERNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_EMAIL));
    }
}
