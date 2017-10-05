<?php
namespace Chamilo\Configuration\Category\Table\Browser;

use Chamilo\Configuration\Category\Interfaces\CategoryVisibilitySupported;
use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.common.category_manager.component.category_browser
 */
/**
 * Cell renderer for the learning object browser table
 */
class CategoryTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    // Inherited
    public function render_cell($column, $category)
    {
        $category_class_name = get_class($category);
        $class_name = $category_class_name::class_name();

        switch ($column->get_name())
        {
            case CategoryTableColumnModel::CATEGORY :
                $img = Theme::getInstance()->getCommonImagePath('TreemenuTypes/Category');
                return '<img src="' . $img . '"alt="category" />';
            case PlatformCategory::PROPERTY_NAME :
                $url = $this->get_component()->get_browse_categories_url($category->get_id());
                return '<a href="' . $url . '" alt="' . $category->get_name() . '">' . $category->get_name() . '</a>';
            case CategoryTableColumnModel::SUBCATEGORIES :
                $count = $this->get_component()->get_parent()->count_categories(
                    new EqualityCondition(
                        new PropertyConditionVariable($class_name, PlatformCategory::PROPERTY_PARENT),
                        new StaticConditionVariable($category->get_id())));
                return $count;
        }
        return parent::render_cell($column, $category);
    }

    public function get_actions($category)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        $condition = $this->get_component()->get_condition();

        $count = $this->get_component()->get_parent()->count_categories($condition);
        $count_all = $this->get_component()->get_parent()->count_categories();

        /**
         * Added support for CategoryVisibilitySupported marker interface If present, the visibility attribute should be
         * checked.
         */
        if ($category instanceof CategoryVisibilitySupported)
        {
            if ($this->get_component()->get_parent()->allowed_to_change_category_visibility($category->get_id()))
            {
                $image = 'Action/Visible';
                $text = 'Visible';
                if (! $category->get_visibility())
                {
                    $image = 'Action/VisibleNa';
                    $text = 'Invisible';
                }
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get($text, null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath($image),
                        $this->get_component()->get_toggle_visibility_category_url($category->get_id()),
                        ToolbarItem::DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('VisibleNA', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/VisibleNa'),
                        null,
                        ToolbarItem::DISPLAY_ICON));
            }
        }

        if ($this->get_component()->get_parent()->allowed_to_edit_category($category->get_id()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Edit', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Edit'),
                    $this->get_component()->get_update_category_url($category->get_id()),
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
        }

        if ($this->get_component()->supports_impact_view($category->get_id()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Delete'),
                    $this->get_component()->get_impact_view_url($category->get_id()),
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            if ($this->get_component()->get_parent()->allowed_to_delete_category($category->get_id()))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/Delete'),
                        $this->get_component()->get_delete_category_url($category->get_id()),
                        ToolbarItem::DISPLAY_ICON,
                        true));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/DeleteNa'),
                        null,
                        ToolbarItem::DISPLAY_ICON,
                        true));
            }
        }

        if ($category->get_display_order() > 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveUp', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Up'),
                    $this->get_component()->get_move_category_url($category->get_id(), - 1),
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveUpNA', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/UpNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON));
        }

        if ($category->get_display_order() < $count)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveDown', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/Down'),
                    $this->get_component()->get_move_category_url($category->get_id(), 1),
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveDownNA', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getCommonImagePath('Action/DownNa'),
                    null,
                    ToolbarItem::DISPLAY_ICON));
        }

        if ($this->get_component()->get_subcategories_allowed())
        {
            if ($count_all > 1)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Move', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/Move'),
                        $this->get_component()->get_change_category_parent_url($category->get_id()),
                        ToolbarItem::DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('MoveNA', null, Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/MoveNa'),
                        null,
                        ToolbarItem::DISPLAY_ICON));
            }
        }

        return $toolbar->as_html();
    }
}
