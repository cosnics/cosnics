<?php
namespace Chamilo\Core\User\Table\Admin;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Table to display a set of users.
 */
class AdminUserTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_USER_USER_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE_USER)), 
                Translation::get('RemoveSelected', null, StringUtilities::LIBRARIES)));
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_ACTIVATE)), 
                Translation::get('ActivateSelected', null, StringUtilities::LIBRARIES),
                false));
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DEACTIVATE)), 
                Translation::get('DeactivateSelected', null, StringUtilities::LIBRARIES)));
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_RESET_PASSWORD_MULTI)), 
                Translation::get('ResetPassword')));
        
        if (Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'active_online_email_editor')))
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_EMAIL)), 
                    Translation::get('EmailSelected'), 
                    false));
        }
        
        return $actions;
    }
}
