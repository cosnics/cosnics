<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\Table\Merger;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

class MergerTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_SURVEY_PAGE_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        $actions->add_form_action(
            new TableFormAction(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_SELECT_QUESTIONS), 
                Translation :: get('AddSelectedQuestions'), 
                false));
        return $actions;
    }
}
?>