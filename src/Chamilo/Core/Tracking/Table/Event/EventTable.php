<?php
namespace Chamilo\Core\Tracking\Table\Event;

use Chamilo\Core\Tracking\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

class EventTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_EVENT_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        $actions->add_form_action(
            new TableFormAction(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_ACTIVATE_EVENT), 
                Translation :: get('EnableSelectedEvents'), 
                false));
        $actions->add_form_action(
            new TableFormAction(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_DEACTIVATE_EVENT), 
                Translation :: get('DisableSelectedEvents'), 
                false));
        $actions->add_form_action(
            new TableFormAction(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_EMPTY_EVENT_TRACKERS), 
                Translation :: get('EmptySelectedEvents')));
        return $actions;
    }
}
