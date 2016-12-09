<?php
namespace Chamilo\Application\Weblcms\Course\Table\CourseTable;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class describes the default cell renderer for the course table
 *
 * @package \application\weblcms\course
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Renders a cell for a given object
     *
     * @param $column \libraries\ObjectTableColumn
     *
     * @param mixed $course
     *
     * @return String
     */
    public function render_cell($column, $course)
    {
        if ($column instanceof DataClassPropertyTableColumn)
        {
            switch ($column->get_class_name())
            {
                case Course :: class_name() :
                    {
                        switch ($column->get_name())
                        {
                            case Course :: PROPERTY_TITLE :
                                $course_title = parent :: render_cell($column, $course);

                                return $course_title;
                            case Course :: PROPERTY_TITULAR_ID :
                                return \Chamilo\Core\User\Storage\DataManager :: get_fullname_from_user(
                                    $course[Course :: PROPERTY_TITULAR_ID],
                                    Translation :: get('TitularUnknown'));
                        }
                        break;
                    }
                case CourseType :: class_name() :
                    {
                        if ($column->get_name() == CourseType :: PROPERTY_TITLE)
                        {
                            $course_type_title = $course[Course :: PROPERTY_COURSE_TYPE_TITLE];
                            return ! $course_type_title ? Translation :: get('NoCourseType') : $course_type_title;
                        }
                    }
            }
        }

        return parent :: render_cell($column, $course);
    }

    /**
     * Returns the actions toolbar
     *
     * @param mixed $course
     *
     * @return String
     */
    public function get_actions($course)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('ViewCourseHome'),
                Theme :: getInstance()->getCommonImagePath('Action/Home'),
                $this->get_component()->get_view_course_home_url($course[Course :: PROPERTY_ID]),
                ToolbarItem :: DISPLAY_ICON));

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                $this->get_component()->get_update_course_url($course[Course :: PROPERTY_ID]),
                ToolbarItem :: DISPLAY_ICON));

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->get_component()->get_delete_course_url($course[Course :: PROPERTY_ID]),
                ToolbarItem :: DISPLAY_ICON,
                true));

        return $toolbar->as_html();
    }
}
