<?php
namespace Chamilo\Core\Repository\External\Table\Export;

use Chamilo\Core\Repository\External\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;

class ExportTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_EXTERNAL_REPOSITORY_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        
        // $actions->add_form_action(new TableFormAction(array(Manager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION =>
        // Manager :: ACTION_EXPORT_EXTERNAL_REPOSITORY), Translation
        // :: get('ExportSelected', null, Utilities :: COMMON_LIBRARIES)));
        
        return $actions;
    }
}