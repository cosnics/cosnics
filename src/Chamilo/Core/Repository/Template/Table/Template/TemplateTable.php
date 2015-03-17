<?php
namespace Chamilo\Core\Repository\Template\Table\Template;

use Chamilo\Core\Repository\Table\ContentObject\Table\RepositoryTable;
use Chamilo\Core\Repository\Template\Manager;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class TemplateTable extends RepositoryTable
{

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        $actions->add_form_action(
            new TableFormAction(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_DELETE), 
                Translation :: get('RemoveSelected', null, Utilities :: COMMON_LIBRARIES)));
        $actions->add_form_action(
            new TableFormAction(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_COPY), 
                Translation :: get('CopySelectedToRepository')));
        return $actions;
    }
}
