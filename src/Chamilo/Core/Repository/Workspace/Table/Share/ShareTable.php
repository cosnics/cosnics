<?php
namespace Chamilo\Core\Repository\Workspace\Table\Share;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ShareTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_SELECTED_WORKSPACE_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_SHARE)), 
                Translation::get('ShareSelected', null, Utilities::COMMON_LIBRARIES), 
                false));
        
        return $actions;
    }
}
