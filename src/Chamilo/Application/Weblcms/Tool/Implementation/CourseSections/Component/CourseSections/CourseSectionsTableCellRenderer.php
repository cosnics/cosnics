<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component\CourseSections;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Manager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.tool.course_sections.component.course_sections_browser
 */
/**
 * Cell rendere for the learning object browser table
 */
class CourseSectionsTableCellRenderer extends DataClassTableCellRenderer implements
    TableCellRendererActionsColumnSupport
{

    public function get_actions($course_section)
    {
        $toolbar = new Toolbar();

        $filter = array(
            CourseSection::TYPE_DISABLED,
            CourseSection::TYPE_TOOL,
            CourseSection::TYPE_LINK,
            CourseSection::TYPE_ADMIN);

        if (! in_array($course_section->get_type(), $filter))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Edit', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Edit'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_UPDATE_COURSE_SECTION,
                            Manager::PARAM_COURSE_SECTION_ID => $course_section->get_id())),
                    ToolbarItem::DISPLAY_ICON));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Delete'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_REMOVE_COURSE_SECTION,
                            Manager::PARAM_COURSE_SECTION_ID => $course_section->get_id())),
                    ToolbarItem::DISPLAY_ICON,
                    true));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('SelectTools'),
                    Theme::getInstance()->getCommonImagePath('Action/Move'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_SELECT_TOOLS_COURSE_SECTION,
                            Manager::PARAM_COURSE_SECTION_ID => $course_section->get_id())),
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('EditNA', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/EditNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON));
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('DeleteNA', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/DeleteNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON));
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('SelectToolsNA'),
                    Theme::getInstance()->getCommonImagePath('Action/MoveNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON));
        }

        $order = $course_section->get_display_order();

        if ($order == 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveUpNA', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/UpNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveUp', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Up'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_MOVE_COURSE_SECTION,
                            Manager::PARAM_COURSE_SECTION_ID => $course_section->get_id(),
                            Manager::PARAM_DIRECTION => - 1)),
                    ToolbarItem::DISPLAY_ICON));
        }

        if ($order == $this->count)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveDownNA', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/DownNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveDown', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Down'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_MOVE_COURSE_SECTION,
                            Manager::PARAM_COURSE_SECTION_ID => $course_section->get_id(),
                            Manager::PARAM_DIRECTION => 1)),
                    ToolbarItem::DISPLAY_ICON));
        }

        if ($course_section->get_type() != CourseSection::TYPE_ADMIN)
        {
            if ($course_section->is_visible())
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('ChangeVisible'),
                        Theme::getInstance()->getCommonImagePath('Action/Visible'),
                        $this->get_component()->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_CHANGE_COURSE_SECTION_VISIBILITY,
                                Manager::PARAM_COURSE_SECTION_ID => $course_section->get_id())),
                        ToolbarItem::DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('ChangeVisible'),
                        Theme::getInstance()->getCommonImagePath('Action/Invisible'),
                        $this->get_component()->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_CHANGE_COURSE_SECTION_VISIBILITY,
                                Manager::PARAM_COURSE_SECTION_ID => $course_section->get_id())),
                        ToolbarItem::DISPLAY_ICON));
            }
        }

        return $toolbar->as_html();
    }
}
