<?php
namespace Chamilo\Core\Group\Table\SubscribeUser;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * Table to display a list of users not subscribed to a course.
 */
class SubscribeUserTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_USER_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(Manager::context(), self::TABLE_IDENTIFIER);
        
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(Manager::PARAM_ACTION => Manager::ACTION_SUBSCRIBE_USER_TO_GROUP)), 
                Translation::get('SubscribeSelected'), 
                false));
        
        return $actions;
    }
}
