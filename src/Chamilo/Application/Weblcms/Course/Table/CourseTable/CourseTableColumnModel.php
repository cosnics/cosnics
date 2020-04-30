<?php
namespace Chamilo\Application\Weblcms\Course\Table\CourseTable;

use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * This class describes the column model for the course table
 * 
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
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
        $this->add_column(new DataClassPropertyTableColumn(Course::class, Course::PROPERTY_VISUAL_CODE));
        $this->add_column(new DataClassPropertyTableColumn(Course::class, Course::PROPERTY_TITLE));
        
        $this->add_column(
            new DataClassPropertyTableColumn(Course::class, Course::PROPERTY_TITULAR_ID, null, false));
        
        $this->add_column(
            new DataClassPropertyTableColumn(
                CourseType::class,
                CourseType::PROPERTY_TITLE, 
                Translation::get('CourseType')));
    }
}
