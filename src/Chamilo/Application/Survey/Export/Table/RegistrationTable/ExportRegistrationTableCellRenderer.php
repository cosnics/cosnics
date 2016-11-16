<?php
namespace Chamilo\Application\Survey\Export\Table\RegistrationTable;

use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class ExportRegistrationTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    public function get_actions($object)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Activate'), 
                Theme::getInstance()->getCommonImagePath('Action/Confirm'), 
                $this->component->get_export_template_create_url($object), 
                ToolbarItem::DISPLAY_ICON, 
                true));
        
        return $toolbar->as_html();
    }
}
?>