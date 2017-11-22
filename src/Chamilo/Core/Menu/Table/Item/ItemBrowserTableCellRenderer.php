<?php
namespace Chamilo\Core\Menu\Table\Item;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\ItemTitle;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 *
 * @package Chamilo\Core\Menu\Table\Item
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ItemBrowserTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $item)
    {
        switch ($column->get_name())
        {
            case ItemTitle::PROPERTY_TITLE :
                return $item->get_titles()->get_current_translation();
            case 'Type' :
                return '<img src="' . Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Menu',
                    'Types/' . Item::type_integer($item->get_type())) . '" />';
        }

        return parent::render_cell($column, $item);
    }

    public function get_actions($menu)
    {
        $order = $menu->get_sort();
        $max = DataManager::count(
            Item::class_name(),
            new DataClassCountParameters($this->get_component()->get_condition()));

        if ($max == 1)
        {
            $index = 'single';
        }
        else
        {
            if ($order == 1)
            {
                $index = 'first';
            }
            else
            {
                if ($order == $max)
                {
                    $index = 'last';
                }
                else
                {
                    $index = 'middle';
                }
            }
        }

        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES),
                Theme::getInstance()->getCommonImagePath('Action/Edit'),
                $this->get_component()->get_item_editing_url($menu),
                ToolbarItem::DISPLAY_ICON));

        $setting = Configuration::getInstance()->get_setting(array('Chamilo\Core\Menu', 'enable_rights'));

        if ($setting == 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Rights', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Rights'),
                    $this->get_component()->get_item_rights_url($menu),
                    ToolbarItem::DISPLAY_ICON));
        }

        if ($index == 'first' || $index == 'single')
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
                    $this->get_component()->get_item_moving_url($menu, 'up'),
                    ToolbarItem::DISPLAY_ICON));
        }

        if ($index == 'last' || $index == 'single')
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
                    $this->get_component()->get_item_moving_url($menu, 'down'),
                    ToolbarItem::DISPLAY_ICON));
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                Theme::getInstance()->getCommonImagePath('Action/Delete'),
                $this->get_component()->get_item_deleting_url($menu),
                ToolbarItem::DISPLAY_ICON,
                true));

        return $toolbar->as_html();
    }
}
