<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\CourseGroup;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * $Id: course_group_subscribed_user_browser_table_column_model.class.php 216
 * 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.course_group.component.user_table
 */
class CourseGroupTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
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
        $this->add_column(new DataClassPropertyTableColumn(CourseGroup :: class_name(), CourseGroup :: PROPERTY_NAME));
        $this->add_column(
            new DataClassPropertyTableColumn(CourseGroup :: class_name(), CourseGroup :: PROPERTY_DESCRIPTION));
        $this->add_column(
            new DataClassPropertyTableColumn(CourseGroup :: class_name(), CourseGroup :: PROPERTY_MAX_NUMBER_OF_MEMBERS));
    }
}
