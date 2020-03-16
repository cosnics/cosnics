<?php
namespace Chamilo\Application\Weblcms\Course\OpenCourse\Table;

use Chamilo\Application\Weblcms\Course\OpenCourse\Manager;
use Chamilo\Application\Weblcms\Course\OpenCourse\Service\OpenCourseService;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Course\Table\CourseTable\CourseTableCellRenderer;
use Chamilo\Core\User\Roles\Storage\DataClass\Role;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Cell renderer for the open courses table
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class OpenCourseTableCellRenderer extends CourseTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     *
     * @return OpenCourseService
     */
    public function getOpenCourseService()
    {
        return $this->get_component()->getOpenCourseService();
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
        $translator = Translation::getInstance();

        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->getTranslation('ViewCourseHome', null, Manager::context()), new FontAwesomeGlyph('home'),
                $this->get_component()->getViewCourseUrl($course[Course::PROPERTY_ID]), ToolbarItem::DISPLAY_ICON
            )
        );

        if ($this->get_component()->isAuthorized(Manager::context(), 'ManageOpenCourses'))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->getTranslation('Edit', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('pencil'),
                    $this->get_component()->getUpdateOpenCourseUrl($course[Course::PROPERTY_ID]),
                    ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->getTranslation('Delete', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('times'),
                    $this->get_component()->getDeleteOpenCourseUrl($course[Course::PROPERTY_ID]),
                    ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->render();
    }

    /**
     * Renders a cell for a given object
     *
     * @param TableColumn $column
     * @param array $courseRecord
     *
     * @return String
     */
    public function render_cell($column, $courseRecord)
    {
        if ($column instanceof DataClassPropertyTableColumn)
        {
            switch ($column->get_class_name())
            {
                case Course::class_name() :
                {
                    switch ($column->get_name())
                    {
                        case Course::PROPERTY_TITLE :
                            $course_title = parent::render_cell($column, $courseRecord);
                            $courseViewerUrl = $this->get_component()->getViewCourseUrl(
                                $courseRecord[Course::PROPERTY_ID]
                            );

                            return '<a href="' . $courseViewerUrl . '">' . $course_title . '</a>';
                    }

                    break;
                }
                case Role::class_name() :
                {
                    switch ($column->get_name())
                    {
                        case Role::PROPERTY_ROLE :
                            $courseObject = new Course($courseRecord);
                            $roles = $this->getOpenCourseService()->getRolesForOpenCourse($courseObject);

                            $rolesHtml = array();

                            $rolesHtml[] = '<select>';
                            while ($role = $roles->next_result())
                            {
                                $rolesHtml[] = '<option>' . $role->getRole() . '</option>';
                            }
                            $rolesHtml[] = '</select>';

                            return implode(PHP_EOL, $rolesHtml);
                    }
                }
            }
        }

        return parent::render_cell($column, $courseRecord);
    }
}
