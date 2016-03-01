<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Version;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

class VersionTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_CONTENT_OBJECT_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_COMPARE_CONTENT_OBJECTS)),
                Translation :: get('CompareSelected'),
                false));
        return $actions;
    }
}
