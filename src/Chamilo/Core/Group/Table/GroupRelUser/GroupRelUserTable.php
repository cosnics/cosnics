<?php
namespace Chamilo\Core\Group\Table\GroupRelUser;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;

class GroupRelUserTable extends DataClassTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_GROUP_REL_USER_ID;

    public function getTableActions(): TableFormActions
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(Manager::PARAM_ACTION => Manager::ACTION_UNSUBSCRIBE_USER_FROM_GROUP)), 
                Translation::get('UnsubscribeSelected'), 
                false));
        return $actions;
    }
}
