<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Component\QuestionBrowser;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Builder\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Platform\Translation;

class QuestionTable extends DataClassTable
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