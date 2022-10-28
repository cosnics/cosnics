<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table\Item;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * Portfolio item table
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTable extends DataClassListTableRenderer implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_STEP;

    /**
     * Returns the implemented form actions
     * 
     * @return TableActions
     */
    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        if ($this->get_component()->canEditComplexContentObjectPathNode($this->get_component()->get_current_node()))
        {
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(
                        array(Manager::PARAM_ACTION => Manager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM)), 
                    Translation::get('RemoveSelected')));
            
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_MOVE)), 
                    Translation::get('MoveSelected'), 
                    false));
        }
        
        if ($this->get_component()->get_parent()->is_allowed_to_set_content_object_rights())
        {
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_RIGHTS)), 
                    Translation::get('ConfigureRightsSelected'), 
                    false));
        }
        
        return $actions;
    }
}