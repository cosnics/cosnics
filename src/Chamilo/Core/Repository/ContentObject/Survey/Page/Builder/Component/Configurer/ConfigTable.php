<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\Configurer;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ConfigTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager :: PARAM_CONFIG_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        $actions->add_form_action(
            new TableFormAction(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_DELETE_CONFIG), 
                Translation :: get('RemoveSelected', array(), Utilities :: COMMON_LIBRARIES), 
                false));
        return $actions;
    }
}
?>