<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type\Table;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class GlossaryViewerTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    private $glossary_item;

    public function render_cell($column, $glossary_item)
    {
        $component = $this->get_component()->get_component();
        
        if (! $this->glossary_item || $this->glossary_item->get_id() != $glossary_item->get_ref())
            $this->glossary_item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                GlossaryItem::class_name(), 
                $glossary_item->get_ref());
        
        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TITLE :
                return $this->glossary_item->get_title();
            case ContentObject::PROPERTY_DESCRIPTION :
                
                return ContentObjectRenditionImplementation::launch(
                    $this->glossary_item, 
                    ContentObjectRendition::FORMAT_HTML, 
                    ContentObjectRendition::VIEW_DESCRIPTION, 
                    $this->get_component());
        }
        return parent::render_cell($column, $glossary_item);
    }

    public function get_actions($glossary_item)
    {
        $component = $this->get_component()->get_component();
        
        $toolbar = new Toolbar();
        
        if ($component->is_allowed_to_edit_content_object())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/Edit'), 
                    $component->get_complex_content_object_item_update_url($glossary_item), 
                    ToolbarItem::DISPLAY_ICON));
        }
        
        if ($component->is_allowed_to_delete_child())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('times'),
                    $component->get_complex_content_object_item_delete_url($glossary_item), 
                    ToolbarItem::DISPLAY_ICON, 
                    true));
        }
        
        return $toolbar->as_html();
    }
}
