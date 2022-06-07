<?php
namespace Chamilo\Application\Weblcms\CourseType\Table\CourseType;

use Chamilo\Application\Weblcms\CourseType\Manager;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class describes the default cell renderer for the course type table
 *
 * @package \application\weblcms\course_type
 * @author Yannick & Tristan
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring
 */
class CourseTypeTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */

    /**
     * Returns the actions toolbar
     *
     * @param $course_type CourseType
     *
     * @return String
     */
    public function get_actions($course_type)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        if ($course_type->is_active())
        {
            $activation_translation = Translation::get('Deactivate', null, StringUtilities::LIBRARIES);
            $glyph = new FontAwesomeGlyph('eye');
        }
        else
        {
            $activation_translation = Translation::get('Activate', null, StringUtilities::LIBRARIES);
            $glyph = new FontAwesomeGlyph('eye', array('text-muted'));
        }

        $toolbar->add_item(
            new ToolbarItem(
                $activation_translation, $glyph,
                $this->get_component()->get_change_course_type_activation_url($course_type->get_id()),
                ToolbarItem::DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->get_component()->get_update_course_type_url($course_type->get_id()), ToolbarItem::DISPLAY_ICON
            )
        );

        if (!DataManager::has_course_type_courses($course_type->get_id()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->get_component()->get_delete_course_type_url($course_type->get_id()),
                    ToolbarItem::DISPLAY_ICON, true
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('DeleteNA', null, StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('times', array('text-muted')), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $display_order = $course_type->get_display_order();

        if ($display_order > 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveUp', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-up'),
                    $this->get_component()->get_move_course_type_url(
                        $course_type->get_id(), Manager::MOVE_DIRECTION_UP
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveUpNA', null, StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-up', array('text-muted')), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $max_objects = $this->get_table()->get_data_provider()->count_data();

        if ($display_order < $max_objects)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveDown', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-down'),
                    $this->get_component()->get_move_course_type_url(
                        $course_type->get_id(), Manager::MOVE_DIRECTION_DOWN
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveDownNA', null, StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-down', array('text-muted')), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->as_html();
    }

    /**
     * Renders a cell for a given object
     *
     * @param $column \libraries\ObjectTableColumn
     *
     * @param $course_type CourseType
     *
     * @return String
     */
    public function render_cell($column, $course_type)
    {
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
            case CourseType::PROPERTY_TITLE :
                $name = parent::render_cell($column, $course_type);
                $name_short = $name;
                if (strlen($name_short) > 53)
                {
                    $name_short = mb_substr($name_short, 0, 50) . '&hellip;';
                }

                return '<a href="' . $this->get_component()->get_view_course_type_url($course_type->get_id()) .
                    '" title="' . htmlentities($name) . '">' . $name_short . '</a>';

            case CourseType::PROPERTY_DESCRIPTION :
                $description = strip_tags(parent::render_cell($column, $course_type));
                if (strlen($description) > 175)
                {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }

                return $description;

            case CourseType::PROPERTY_ACTIVE :

                if ($course_type->is_active())
                {
                    $glyph = new FontAwesomeGlyph(
                        'circle', array('text-success'),
                        Translation::get('ConfirmTrue', null, StringUtilities::LIBRARIES), 'fas'
                    );
                }
                else

                {
                    $glyph = new FontAwesomeGlyph(
                        'circle', array('text-danger'),
                        Translation::get('ConfirmFalse', null, StringUtilities::LIBRARIES), 'fas'
                    );
                }

                return $glyph->render();
        }

        return parent::render_cell($column, $course_type);
    }
}
