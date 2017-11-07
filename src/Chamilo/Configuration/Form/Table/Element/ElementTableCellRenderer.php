<?php
namespace Chamilo\Configuration\Form\Table\Element;

use Chamilo\Configuration\Form\Storage\DataClass\Element;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table cell renderer for the schema
 * 
 * @package configuration\form
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Renders a single cell
     * 
     * @param TableColumn $column
     * @param Element $result
     *
     * @return string
     */
    public function render_cell($column, $result)
    {
        switch ($column->get_name())
        {
            case Element::PROPERTY_TYPE :
                return $result->get_type_name($result->get_type());
            case Element::PROPERTY_REQUIRED :
                if ($result->get_required())
                {
                    return Translation::get('ConfirmTrue', null, Utilities::COMMON_LIBRARIES);
                }
                else
                {
                    return Translation::get('ConfirmFalse', null, Utilities::COMMON_LIBRARIES);
                }
        }
        
        return parent::render_cell($column, $result);
    }

    /**
     * Returns the actions toolbar
     * 
     * @param Element $result
     *
     * @return String
     */
    public function get_actions($result)
    {
        $update_url = $this->get_component()->get_update_element_url($result);
        $delete_url = $this->get_component()->get_delete_element_url($result);
        
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Edit'), 
                $update_url, 
                ToolbarItem::DISPLAY_ICON));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                $delete_url, 
                ToolbarItem::DISPLAY_ICON, 
                true));
        
        return $toolbar->as_html();
    }
}