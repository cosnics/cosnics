<?php
namespace Chamilo\Core\Metadata\Vocabulary\Table\Select;

use Chamilo\Core\Metadata\Vocabulary\Manager;
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
class SelectTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
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
            case SelectTableColumnModel :: COLUMN_TYPE :
                
                if ($result->get_user_id() == 0)
                {
                    $image = 'Action/Value/Predefined';
                    $translationVariable = 'Predefined';
                }
                else
                {
                    $image = 'Action/Value/User';
                    $translationVariable = 'UserDefined';
                }
                
                return Theme :: getInstance()->getImage(
                    $image, 
                    'png', 
                    Translation :: get($translationVariable, null, $this->get_component()->package()), 
                    null, 
                    ToolbarItem :: DISPLAY_ICON, 
                    false, 
                    'Chamilo\Core\Metadata\Element');
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
                Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagePath('Action/Add'), 
                $this->get_component()->get_url(
                    array(
                        \Chamilo\Core\Metadata\Element\Manager :: PARAM_ELEMENT_ID => $this->get_component()->getSelectedElementId(), 
                        \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager :: PARAM_ACTION => \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager :: ACTION_SELECT, 
                        Manager :: PARAM_VOCABULARY_ID => $result->get_id())), 
                ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}