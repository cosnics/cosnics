<?php
namespace Chamilo\Core\Repository\Table\Complex;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: complex_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib.repository_manager.component.complex_browser
 */
class ComplexTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{
    
    // Inherited
    public function render_cell($column, $cloi)
    {
        $content_object = $cloi->get_ref_object();
        
        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TYPE :
                $type = $content_object->get_type();
                $icon = $content_object->get_icon_path();
                $url = '<img src="' . $icon . '" alt="' . htmlentities(
                    Translation::get('TypeName', null, ContentObject::get_content_object_type_namespace($type))) . '"/>';
                return $url;
            case Theme::getInstance()->getCommonImage(
                'Action/Category', 
                'png', 
                Translation::get('Type'), 
                null, 
                ToolbarItem::DISPLAY_ICON) :
                return $content_object->get_icon_image(Theme::ICON_MINI);
            
            case ContentObject::PROPERTY_TITLE :
                $title = htmlspecialchars($content_object->get_title());
                $title_short = $title;
                $title_short = StringUtilities::getInstance()->truncate($title_short, 53, false);
                
                if ($content_object instanceof ComplexContentObjectSupport)
                {
                    $title_short = '<a href="' .
                         $this->get_component()->get_url(
                            array(
                                \Chamilo\Core\Repository\Builder\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $cloi->get_id())) .
                         '">' . $title_short . '</a>';
                }
                else
                {
                    $title_short = '<a href="' . $this->getTitleLink($cloi) . '">' . $title_short . '</a>';
                }
                
                return $title_short;
            case ContentObject::PROPERTY_DESCRIPTION :
                $description = $content_object->get_description();
                return StringUtilities::getInstance()->truncate($description, 75);
            case Translation::get(ComplexTableColumnModel::SUBITEMS) :
                if ($cloi->is_complex())
                {
                    $condition = new EqualityCondition(
                        ComplexContentObjectItem::PROPERTY_PARENT, 
                        $cloi->get_ref(), 
                        ComplexContentObjectItem::get_table_name());
                    return DataManager::count_complex_content_object_items(
                        ComplexContentObjectItem::class_name(), 
                        $condition);
                }
                return 0;
        }
        
        return parent::render_cell($column, $cloi);
    }

    /**
     * Returns the link for the title
     *
     * @param ComplexContentObjectItem $complexContentObjectItem
     *
     * @return string
     */
    protected function getTitleLink($complexContentObjectItem)
    {
        return $this->get_component()->get_complex_content_object_item_edit_url($complexContentObjectItem->getId());
    }

    public function get_actions($cloi)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Edit'), 
                $this->get_component()->get_complex_content_object_item_edit_url($cloi->get_id()), 
                ToolbarItem::DISPLAY_ICON));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('CopyEdit', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Copy'), 
                $this->get_component()->get_complex_content_object_item_copy_url($cloi->get_id()), 
                ToolbarItem::DISPLAY_ICON, 
                true));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                $this->get_component()->get_complex_content_object_item_delete_url($cloi->get_id()), 
                ToolbarItem::DISPLAY_ICON, 
                true));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('ChangeParent', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Move'), 
                $this->get_component()->get_complex_content_object_parent_changer_url($cloi->get_id()), 
                ToolbarItem::DISPLAY_ICON));
        
        $allowed = $this->check_move_allowed($cloi);
        
        if ($allowed["moveup"])
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveUp', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/Up'), 
                    $this->get_component()->get_complex_content_object_item_move_url(
                        $cloi->get_id(), 
                        Manager::PARAM_DIRECTION_UP), 
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveUpNotAvailable', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/UpNa'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON));
        }
        
        if ($allowed["movedown"])
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveDown', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/Down'), 
                    $this->get_component()->get_complex_content_object_item_move_url(
                        $cloi->get_id(), 
                        Manager::PARAM_DIRECTION_DOWN), 
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('MoveDownNotAvailable', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/DownNa'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }

    protected function check_move_allowed($cloi)
    {
        $moveup_allowed = true;
        $movedown_allowed = true;
        
        $count = DataManager::count_complex_content_object_items(
            ComplexContentObjectItem::class_name(), 
            $this->get_component()->get_table_condition(__CLASS__));
        if ($count == 1)
        {
            $moveup_allowed = false;
            $movedown_allowed = false;
        }
        else
        {
            if ($cloi->get_display_order() == 1)
            {
                $moveup_allowed = false;
            }
            else
            {
                if ($cloi->get_display_order() == $count)
                {
                    $movedown_allowed = false;
                }
            }
        }
        
        return array('moveup' => $moveup_allowed, 'movedown' => $movedown_allowed);
    }
}
