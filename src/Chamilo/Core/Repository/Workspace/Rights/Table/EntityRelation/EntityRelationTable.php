<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Table\EntityRelation;

use Chamilo\Core\Repository\Workspace\Rights\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class EntityRelationTable extends DataClassTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_ENTITY_RELATION_ID;

    public function getTableActions(): TableFormActions
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE)), 
                Translation::get('DeleteSelected', null, StringUtilities::LIBRARIES),
                true));
        
        return $actions;
    }
}
