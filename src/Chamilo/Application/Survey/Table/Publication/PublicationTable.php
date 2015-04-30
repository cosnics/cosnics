<?php
namespace Chamilo\Application\Survey\Table\Publication;

use Chamilo\Application\Survey\Component\BrowserComponent;
use Chamilo\Application\Survey\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class PublicationTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_PUBLICATION_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        
        switch ($this->get_component()->get_table_type())
        {
            case BrowserComponent :: TAB_MY_PUBLICATIONS :
                $actions->add_form_action(
                    new TableFormAction(
                        Manager :: ACTION_DELETE, 
                        Translation :: get('RemoveSelected', array(), Utilities :: COMMON_LIBRARIES), 
                        true));
                break;
            case BrowserComponent :: TAB_EXPORT :
                $actions->add_form_action(
                    new TableFormAction(
                        Manager :: ACTION_EXPORT, 
                        Translation :: get('ExportToExcel', array(), Utilities :: COMMON_LIBRARIES), 
                        true));
                break;
        }
        
        return $actions;
    }
}
?>