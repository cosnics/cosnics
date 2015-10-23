<?php
namespace Chamilo\Core\User\Table\Admin;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Table to display a set of users.
 */
class AdminUserTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_USER_USER_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_DELETE_USER)),
                Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)));
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_ACTIVATE)),
                Translation :: get('ActivateSelected', null, Utilities :: COMMON_LIBRARIES),
                false));
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_DEACTIVATE)),
                Translation :: get('DeactivateSelected', null, Utilities :: COMMON_LIBRARIES)));
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_RESET_PASSWORD_MULTI)),
                Translation :: get('ResetPassword')));

        if (PlatformSetting :: get('active_online_email_editor'))
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_EMAIL)),
                    Translation :: get('EmailSelected'),
                    false));
        }

        return $actions;
    }
}
