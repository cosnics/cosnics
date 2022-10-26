<?php
namespace Chamilo\Core\Group\Table\Group;

use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class GroupTable extends DataClassTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_GROUP_ID;

    public function getTableActions(): TableFormActions
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE_GROUP)), 
                Translation::get('RemoveSelected', null, StringUtilities::LIBRARIES)));
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_TRUNCATE_GROUP)), 
                Translation::get('TruncateSelected')));
        return $actions;
    }
}
