<?php
namespace Chamilo\Application\Weblcms\CourseType\Table\CourseType;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * @package Chamilo\Application\Weblcms\CourseType\Table\CourseType
 *
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CourseTypeTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const DEFAULT_ORDER_COLUMN_INDEX = 3;

    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(CourseType::class_name(), CourseType::PROPERTY_TITLE, false)
        );
        $this->add_column(
            new DataClassPropertyTableColumn(CourseType::class_name(), CourseType::PROPERTY_DESCRIPTION, false)
        );
        $this->add_column(
            new DataClassPropertyTableColumn(CourseType::class_name(), CourseType::PROPERTY_ACTIVE, false)
        );
        $this->add_column(
            new DataClassPropertyTableColumn(CourseType::class_name(), CourseType::PROPERTY_DISPLAY_ORDER, false)
        );
    }
}
