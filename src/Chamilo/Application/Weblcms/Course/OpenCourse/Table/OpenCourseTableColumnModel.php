<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Table;

use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Table\CourseTable\CourseTableColumnModel;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * This class describes the column model for the open course table
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseTableColumnModel extends CourseTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     * Initializes the columns for the table
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Course::class_name(), Course::PROPERTY_VISUAL_CODE));
        $this->add_column(new DataClassPropertyTableColumn(Course::class_name(), Course::PROPERTY_TITLE));

        $this->add_column(
            new DataClassPropertyTableColumn(Course::class_name(), Course::PROPERTY_TITULAR_ID, null, false)
        );

        if ($this->get_component()->isAuthorized(Manager::context(), 'ManageOpenCourses'))
        {
            $this->add_column(
                new DataClassPropertyTableColumn(
                    Role::class_name(),
                    Role::PROPERTY_ROLE,
                    Translation::getInstance()->getTranslation('Role', null, Manager::context()),
                    false
                )
            );
        }
    }
}
