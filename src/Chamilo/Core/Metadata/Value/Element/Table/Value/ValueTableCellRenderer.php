<?php
namespace Chamilo\Core\Metadata\Value\Element\Table\Value;

use Chamilo\Core\Metadata\Value\Element\Manager;
use Chamilo\Core\Metadata\Value\Element\Storage\DataClass\DefaultElementValue;
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
class ValueTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Renders a single cell
     * 
     * @param TableColumn $column
     * @param DataClass $result
     *
     * @return string
     */
    public function render_cell($column, $result)
    {
        switch ($column->get_name())
        {
            case DefaultElementValue :: PROPERTY_VALUE :
                if ($result->get_value())
                {
                    return $result->get_value();
                }
                elseif ($result->get_element_vocabulary_id())
                {
                    $controlled_vocabulary = \Chamilo\Core\Metadata\ControlledVocabulary\Storage\DataManager :: retrieve_by_id(
                        \Chamilo\Core\Metadata\ControlledVocabulary\Storage\DataClass\ControlledVocabulary :: class_name(), 
                        $result->get_element_vocabulary_id());
                    
                    return $controlled_vocabulary->get_value();
                }
                break;
        }
        
        return parent :: render_cell($column, $result);
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
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagePath() . 'action_edit.png', 
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_UPDATE, 
                        Manager :: PARAM_ELEMENT_VALUE_ID => $result->get_id())), 
                ToolbarItem :: DISPLAY_ICON));
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagePath() . 'action_delete.png', 
                $this->get_component()->get_url(
                    array(
                        Manager :: PARAM_ACTION => Manager :: ACTION_DELETE, 
                        Manager :: PARAM_ELEMENT_VALUE_ID => $result->get_id())), 
                ToolbarItem :: DISPLAY_ICON, 
                true));
        
        return $toolbar->as_html();
    }
}