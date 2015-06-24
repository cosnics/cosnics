<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Table\Item;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Portfolio item table
 * 
 * @package repository\content_object\page\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_STEP;

    /**
     * Returns the implemented form actions
     * 
     * @return TableFormActions
     */
    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        
        if ($this->get_component()->get_parent()->is_allowed_to_edit_content_object(
            $this->get_component()->get_current_node()))
        {
            $actions->add_form_action(
                new TableFormAction(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM), 
                    Translation :: get('RemoveSelected')));
          
        }
               
        return $actions;
    }
}