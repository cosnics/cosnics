<?php
namespace Chamilo\Application\Weblcms\CourseType\Table\CourseType;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 * This class describes the column model for the course type table
 * 
 * @package \application\weblcms\course_type
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTypeTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(
            new DataClassPropertyTableColumn(CourseType::class_name(), CourseType::PROPERTY_TITLE, null, false));
        $this->add_column(
            new DataClassPropertyTableColumn(CourseType::class_name(), CourseType::PROPERTY_DESCRIPTION, null, false));
        $this->add_column(
            new DataClassPropertyTableColumn(CourseType::class_name(), CourseType::PROPERTY_ACTIVE, null, false));
    }
}
