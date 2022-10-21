<?php
namespace Chamilo\Core\Repository\External\Table\Export;

use Chamilo\Core\Repository\External\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;

class ExportTable extends DataClassTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_EXTERNAL_REPOSITORY_ID;

    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        // $actions->add_form_action(
        // new TableFormAction(
        // $this->get_component()->get_url( array(
        // Manager::PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => Manager::ACTION_EXPORT_EXTERNAL_REPOSITORY)),
        // Translation::get('ExportSelected', null, StringUtilities::LIBRARIES)));
        
        return $actions;
    }
}