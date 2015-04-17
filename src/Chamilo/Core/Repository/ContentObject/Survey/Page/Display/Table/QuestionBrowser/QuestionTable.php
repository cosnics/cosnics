<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Table\QuestionBrowser;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;

class QuestionTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_COMPLEX_QUESTION_ITEM_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        $actions->add_form_action(
            new TableFormAction(
                array(Manager :: PARAM_ACTION => Manager :: ACTION_CHANGE_QUESTION_VISIBILITY), 
                Translation :: get('ToggleVisibility'), 
                false));
        return $actions;
    }
}