<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\Table\Browser;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * $Id: survey_page_browser_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_builder.survey_page.component.browser
 */
/**
 * Cell rendere for the learning object browser table
 */
class ComplexTableCellRenderer extends ComplexTableCellRenderer
{

    function render_cell($column, $complex_content_object_item)
    {
        switch ($column->get_name())
        {
            case Translation :: get(
                (string) StringUtilities :: getInstance()->createString(ContentObject :: PROPERTY_TITLE)->upperCamelize()) :
                $title = htmlspecialchars($complex_content_object_item->get_title());
                $title_short = $title;
                $title_short = StringUtilities :: getInstance()->truncate($title_short, 53, false);

                if ($complex_content_object_item instanceof ComplexContentObjectSupport)
                {
                    $title_short = '<a href="' .
                         $this->get_component()->get_url(
                            array(
                                Manager :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id())) .
                         '">' . $title_short . '</a>';
                }
                else
                {
                    $title_short = '<a href="' . $this->get_component()->get_complex_content_object_item_view_url(
                        $complex_content_object_item->get_id()) . '">' . $title_short . '</a>';
                }

                return $title_short;
                break;

            case Theme :: getInstance()->getImage(
                'display_order',
                'png',
                Translation :: get('DisplayOrder'),
                null,
                ToolbarItem :: DISPLAY_ICON) :

                $html = array();
                $html[] = '<select class="order" id="order_' . $complex_content_object_item->get_id() . '">';

                $parent_object = $complex_content_object_item->get_parent_object();
                $count_questions = $parent_object->count_children();
                for ($i = 1; $i <= $count_questions; $i ++)
                {
                    $html[] = '<option' . ($i == $complex_content_object_item->get_display_order() ? ' selected' : '') .
                         ' value="' . $i . '">' . $i . '</option>';
                }

                $html[] = '</select>';
                return implode(PHP_EOL, $html);
                break;
        }

        return parent :: render_cell($column, $complex_content_object_item);
    }

    public function get_actions($complex_content_object_item)
    {
        $toolbar = parent :: get_actions($complex_content_object_item);
        $parent = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
            $complex_content_object_item->get_parent());

        if ($complex_content_object_item->is_extended() || $this->get_component() instanceof Manager)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Edit'),
                    Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                    $this->get_component()->get_complex_content_object_item_edit_url(
                        $complex_content_object_item->get_id()),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('EditNA'),
                    Theme :: getInstance()->getCommonImagePath('Action/EditNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Delete'),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->get_component()->get_complex_content_object_item_delete_url(
                    $complex_content_object_item->get_id()),
                ToolbarItem :: DISPLAY_ICON,
                true));

        $allowed = $this->check_move_allowed($complex_content_object_item);

        if ($allowed["moveup"])
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveUp'),
                    Theme :: getInstance()->getCommonImagePath('Action/Up'),
                    $this->get_component()->get_complex_content_object_item_move_url(
                        $complex_content_object_item->get_id(),
                        \Chamilo\Core\Repository\Manager :: PARAM_DIRECTION_UP),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveUpNA'),
                    Theme :: getInstance()->getCommonImagePath('Action/UpNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        if ($allowed["movedown"])
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveDown'),
                    Theme :: getInstance()->getCommonImagePath('Action/Down'),
                    $this->get_component()->get_complex_content_object_item_move_url(
                        $complex_content_object_item->get_id(),
                        \Chamilo\Core\Repository\Manager :: PARAM_DIRECTION_DOWN),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('MoveDownNA'),
                    Theme :: getInstance()->getCommonImagePath('Action/DownNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }
}
