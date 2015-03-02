<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\Merger;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class MergerTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $content_object)
    {
        switch ($column->get_name())
        {
            case Theme :: getInstance()->getCommonImage(
                'action_category', 
                'png', 
                Translation :: get('Type'), 
                null, 
                ToolbarItem :: DISPLAY_ICON) :
                return $content_object->get_icon_image(Theme :: ICON_MINI);
            
            case ContentObject :: PROPERTY_TITLE :
                return Utilities :: truncate_string($content_object->get_title(), 50);
            case ContentObject :: PROPERTY_DESCRIPTION :
                return Utilities :: htmlentities(Utilities :: truncate_string($content_object->get_description(), 50));
        }
        
        return parent :: render_cell($column, $content_object);
    }

    public function get_actions($content_object)
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Select'), 
                Theme :: getInstance()->getCommonImagePath('action_right'), 
                $this->get_component()->get_question_selector_url($content_object->get_id()), 
                ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}
?>