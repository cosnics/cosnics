<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Overview\GroupUser;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class CourseGroupUserTableColumnModel extends DataClassTableColumnModel
{
    const COURSE_GROUP_COLOMN = 'CourseGroup';
    
    // **************************************************************************
    // CONSTRUCTOR
    // **************************************************************************
    
    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_OFFICIAL_CODE));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_USERNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_LASTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_FIRSTNAME));
        $this->add_column(new DataClassPropertyTableColumn(User :: class_name(), User :: PROPERTY_EMAIL));
        
        $title = Translation :: get(self :: COURSE_GROUP_COLOMN, array(), Utilities :: COMMON_LIBRARIES);
        $this->add_column(
            new DataClassPropertyTableColumn(CourseGroup :: class_name(), CourseGroup :: PROPERTY_ID, $title, false));
    }
}
