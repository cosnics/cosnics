<?php
namespace Chamilo\Core\User\Table\Approval;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;

/**
 * Table to display a set of users.
 */
class UserApprovalTable extends DataClassTable implements TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_USER_USER_ID;

    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_APPROVE_USER)), 
                Translation::get('ApproveSelected')));
        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DENY_USER)), 
                Translation::get('DenySelected')));
        return $actions;
    }
}
