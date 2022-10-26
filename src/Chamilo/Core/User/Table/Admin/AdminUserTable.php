<?php
namespace Chamilo\Core\User\Table\Admin;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Table to display a set of users.
 */
class AdminUserTable extends DataClassTable implements TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_USER_USER_ID;

    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE_USER)), 
                Translation::get('RemoveSelected', null, StringUtilities::LIBRARIES)));
        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_ACTIVATE)), 
                Translation::get('ActivateSelected', null, StringUtilities::LIBRARIES),
                false));
        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DEACTIVATE)), 
                Translation::get('DeactivateSelected', null, StringUtilities::LIBRARIES)));
        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_RESET_PASSWORD_MULTI)), 
                Translation::get('ResetPassword')));
        
        if (Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'active_online_email_editor')))
        {
            $actions->addAction(
                new TableAction(
                    $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_EMAIL)), 
                    Translation::get('EmailSelected'), 
                    false));
        }
        
        return $actions;
    }
}
