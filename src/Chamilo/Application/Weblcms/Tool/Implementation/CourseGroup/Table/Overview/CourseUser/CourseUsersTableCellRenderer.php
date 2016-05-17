<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Table\Overview\CourseUser;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;

/**
 * Cell renderer for the course users table
 *
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring from ObjectTable to RecordTable
 */
class CourseUsersTableCellRenderer extends RecordTableCellRenderer
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Renders a given cell.
     *
     * @param $column type
     * @param mixed[] $course_user
     *
     * @return string
     */
    public function render_cell($column, $course_user)
    {
        switch ($column->get_name())
        {
            case CourseUsersTableColumnModel :: COLUMN_COURSE_GROUPS :
                {
                    return DataManager :: get_course_groups_from_user_as_string(
                        $course_user[User :: PROPERTY_ID],
                        $this->get_component()->get_course_id());
                }
        }

        return parent :: render_cell($column, $course_user);
    }
}