<?php
namespace Chamilo\Application\Survey\Rights\Table\EntityRelation;

use Chamilo\Application\Survey\Rights\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class EntityRelationTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_ENTITY_RELATION_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self :: TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager :: PARAM_ACTION => Manager :: ACTION_DELETE)),
                Translation :: get('DeleteSelected', null, Utilities :: COMMON_LIBRARIES),
                true));

        return $actions;
    }
}
