<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component\CourseSections;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 *
 * @package application.lib.weblcms.tool.course_sections.component.course_sections_browser
 */
/**
 * Table column model for the course browser table
 */
class CourseSectionsTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(CourseSection::class, CourseSection::PROPERTY_NAME));
    }
}
