<?php
namespace Chamilo\Core\Metadata\Schema\Table\Schema;

use Chamilo\Core\Metadata\Schema\Manager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table cell renderer for the schema
 * 
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SchemaTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Returns the actions toolbar
     * 
     * @param mixed $result
     *
     * @return String
     */
    public function get_actions($result)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        if ($result->is_fixed())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('EditNA', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagesPath() . 'action_edit_na.png', 
                    null, 
                    ToolbarItem :: DISPLAY_ICON));
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('DeleteNA', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagesPath() . 'action_delete_na.png', 
                    null, 
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagesPath() . 'action_edit.png', 
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE, 
                            Manager :: PARAM_SCHEMA_ID => $result->get_id())), 
                    ToolbarItem :: DISPLAY_ICON));
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagesPath() . 'action_delete.png', 
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_DELETE, 
                            Manager :: PARAM_SCHEMA_ID => $result->get_id())), 
                    ToolbarItem :: DISPLAY_ICON, 
                    true));
        }
        
        return $toolbar->as_html();
    }
}