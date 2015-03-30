<?php
namespace Chamilo\Core\Repository\Table\ContentObject\Shared;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class SharedTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_CONTENT_OBJECT_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        
        $actions->add_form_action(
            new TableFormAction(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_MOVE_SHARED_CONTENT_OBJECTS), 
                Translation :: get('MoveSelected', null, Utilities :: COMMON_LIBRARIES), 
                false));
        $actions->add_form_action(
            new TableFormAction(
                array(
                    Manager :: PARAM_ACTION => Manager :: ACTION_PUBLICATION, 
                    \Chamilo\Core\Repository\Publication\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Publication\Manager :: ACTION_PUBLISH), 
                Translation :: get('PublishSelected', null, Utilities :: COMMON_LIBRARIES), 
                false));
        $actions->add_form_action(
            new TableFormAction(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_COPY_CONTENT_OBJECT), 
                Translation :: get('CopySelected', null, Utilities :: COMMON_LIBRARIES), 
                false));
        $actions->add_form_action(
            new TableFormAction(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_DELETE_SHARED_CONTENT_OBJECTS), 
                Translation :: get('DeleteSelected', null, Utilities :: COMMON_LIBRARIES), 
                true));
        
        return $actions;
    }
}
