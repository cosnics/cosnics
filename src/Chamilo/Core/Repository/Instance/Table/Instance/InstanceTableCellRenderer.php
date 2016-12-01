<?php
namespace Chamilo\Core\Repository\Instance\Table\Instance;

use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
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
class InstanceTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Renders a single cell
     * 
     * @param TableColumn $column
     * @param mixed $result
     *
     * @return string
     */
    public function render_cell($column, $result)
    {
        switch ($column->get_name())
        {
            case Instance::PROPERTY_IMPLEMENTATION :
                $name = htmlentities(Translation::get('ImplementationName', null, $result->get_implementation()));
                return '<img src="' . Theme::getInstance()->getImagePath($result->get_implementation(), 'Logo/22') .
                     '" alt="' . $name . '" title="' . $name . '"/>';
                break;
        }
        
        return parent::render_cell($column, $result);
    }

    /**
     * Returns the actions toolbar
     * 
     * @param mixed $result
     *
     * @return String
     */
    public function get_actions($result)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        if ($result->is_enabled())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Deactivate', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/Deactivate'), 
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_DEACTIVATE, 
                            Manager::PARAM_INSTANCE_ID => $result->get_id())), 
                    ToolbarItem::DISPLAY_ICON, 
                    true));
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Activate', null, Utilities::COMMON_LIBRARIES), 
                    Theme::getInstance()->getCommonImagePath('Action/Activate'), 
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_ACTIVATE, 
                            Manager::PARAM_INSTANCE_ID => $result->get_id())), 
                    ToolbarItem::DISPLAY_ICON, 
                    true));
        }
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Edit'), 
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_UPDATE, 
                        Manager::PARAM_INSTANCE_ID => $result->get_id())), 
                ToolbarItem::DISPLAY_ICON));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE, 
                        Manager::PARAM_INSTANCE_ID => $result->get_id())), 
                ToolbarItem::DISPLAY_ICON, 
                true));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('ManageRights', null, \Chamilo\Core\Rights\Manager::context()), 
                Theme::getInstance()->getCommonImagePath('Action/Rights'), 
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_RIGHTS, 
                        Manager::PARAM_INSTANCE_ID => $result->get_id())), 
                ToolbarItem::DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}