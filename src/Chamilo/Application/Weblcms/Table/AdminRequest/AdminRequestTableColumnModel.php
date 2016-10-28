<?php
namespace Chamilo\Application\Weblcms\Table\AdminRequest;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * $Id: admin_course_type_browser_table_column_model.class.php 218 2010-03-10 14:21:26Z yannick $
 * 
 * @package application.lib.weblcms.weblcms_manager.component.admin_course_type_browser
 */

/**
 * Table column model for the course browser table
 */
class AdminRequestTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const USER_NAME = 'user_name';
    const COURSE_NAME = 'course_name';

    public function initialize_columns()
    {
        $this->add_column(new StaticTableColumn(self :: USER_NAME));
        $this->add_column(new StaticTableColumn(self :: COURSE_NAME));
        $this->add_column(
            new DataClassPropertyTableColumn(CourseRequest :: class_name(), CourseRequest :: PROPERTY_SUBJECT));
        $this->add_column(
            new DataClassPropertyTableColumn(CourseRequest :: class_name(), CourseRequest :: PROPERTY_MOTIVATION));
        $this->add_column(
            new DataClassPropertyTableColumn(CourseRequest :: class_name(), CourseRequest :: PROPERTY_CREATION_DATE));
        $this->add_column(
            new DataClassPropertyTableColumn(CourseRequest :: class_name(), CourseRequest :: PROPERTY_DECISION_DATE));
    }
}
