<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type\Table;

use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class GlossaryViewerTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    private $glossary_item;

    public function render_cell($column, $glossary_item)
    {
        $component = $this->get_component()->get_component();
        
        if (! $this->glossary_item || $this->glossary_item->get_id() != $glossary_item->get_ref())
            $this->glossary_item = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
                $glossary_item->get_ref(), 
                GlossaryItem :: class_name());
        
        switch ($column->get_name())
        {
            case GlossaryItem :: PROPERTY_TITLE :
                return $this->glossary_item->get_title();
            case GlossaryItem :: PROPERTY_DESCRIPTION :
                return $this->glossary_item->get_description();
        }
    }

    public function get_actions($glossary_item)
    {
        $component = $this->get_component()->get_component();
        
        $toolbar = new Toolbar();
        
        if ($component->is_allowed_to_edit_content_object())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('action_edit'), 
                    $component->get_complex_content_object_item_update_url($glossary_item), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        if ($component->is_allowed_to_delete_child())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('action_delete'), 
                    $component->get_complex_content_object_item_delete_url($glossary_item), 
                    ToolbarItem :: DISPLAY_ICON, 
                    true));
        }
        
        return $toolbar->as_html();
    }
}
