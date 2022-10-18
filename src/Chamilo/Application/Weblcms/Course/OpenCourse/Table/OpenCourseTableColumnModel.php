<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Table;

use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Table\CourseTable\CourseTableColumnModel;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

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
    public function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(Course::class, Course::PROPERTY_VISUAL_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(Course::class, Course::PROPERTY_TITLE));

        $this->addColumn(
            new DataClassPropertyTableColumn(Course::class, Course::PROPERTY_TITULAR_ID, null, false)
        );

        if ($this->get_component()->isAuthorized(Manager::context(), 'ManageOpenCourses'))
        {
            $this->addColumn(
                new DataClassPropertyTableColumn(
                    Role::class,
                    Role::PROPERTY_ROLE,
                    Translation::getInstance()->getTranslation('Role', null, Manager::context()),
                    false
                )
            );
        }
    }
}
