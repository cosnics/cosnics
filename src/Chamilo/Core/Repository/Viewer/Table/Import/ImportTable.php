<?php
namespace Chamilo\Core\Repository\Viewer\Table\Import;

use Chamilo\Core\Repository\Viewer\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

class ImportTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_ID;
    const DEFAULT_ROW_COUNT = 'all';

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);

        if ($this->get_component()->get_maximum_select() != Manager :: SELECT_SINGLE)
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_PUBLISHER)),
                    Translation :: get('PublishSelected'),
                    false));
        }

        return $actions;
    }
}
