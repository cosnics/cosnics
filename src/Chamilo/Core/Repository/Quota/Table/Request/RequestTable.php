<?php
namespace Chamilo\Core\Repository\Quota\Table\Request;

use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class RequestTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_REQUEST_ID;
    const TYPE_PERSONAL = 1;
    const TYPE_PENDING = 2;
    const TYPE_GRANTED = 3;
    const TYPE_DENIED = 4;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);

        if ($this->get_component()->get_user()->is_platform_admin())
        {
            if ($this->get_component()->get_table_type() == self :: TYPE_PENDING ||
                 $this->get_component()->get_table_type() == self :: TYPE_DENIED)
            {
                $actions->add_form_action(
                    new TableFormAction(
                        $this->get_component()->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_GRANT)),
                        Translation :: get('GrantSelected', null, Utilities :: COMMON_LIBRARIES)));
            }

            if ($this->get_component()->get_table_type() == self :: TYPE_PENDING)
            {
                $actions->add_form_action(
                    new TableFormAction(
                        $this->get_component()->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_DENY)),
                        Translation :: get('DenySelected', null, Utilities :: COMMON_LIBRARIES)));
            }
        }

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_DELETE)),
                Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)));

        return $actions;
    }
}
