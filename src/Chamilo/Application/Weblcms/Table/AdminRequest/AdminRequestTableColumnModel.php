<?php
namespace Chamilo\Application\Weblcms\Table\AdminRequest;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
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

    public function initializeColumns()
    {
        $this->addColumn(new StaticTableColumn(self::USER_NAME));
        $this->addColumn(new StaticTableColumn(self::COURSE_NAME));
        $this->addColumn(
            new DataClassPropertyTableColumn(CourseRequest::class, CourseRequest::PROPERTY_SUBJECT));
        $this->addColumn(
            new DataClassPropertyTableColumn(CourseRequest::class, CourseRequest::PROPERTY_MOTIVATION));
        $this->addColumn(
            new DataClassPropertyTableColumn(CourseRequest::class, CourseRequest::PROPERTY_CREATION_DATE));
        $this->addColumn(
            new DataClassPropertyTableColumn(CourseRequest::class, CourseRequest::PROPERTY_DECISION_DATE));
    }
}
