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

    public function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(CourseType::class, CourseType::PROPERTY_TITLE, false)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(CourseType::class, CourseType::PROPERTY_DESCRIPTION, false)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(CourseType::class, CourseType::PROPERTY_ACTIVE, false)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(CourseType::class, CourseType::PROPERTY_DISPLAY_ORDER, false)
        );
    }
}
