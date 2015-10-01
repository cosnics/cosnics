<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component\AssessmentMerger;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Platform\Session\Request;

class ObjectTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_QUESTION_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        $actions->add_form_action(
            new TableFormAction(
                array(
                    Manager :: PARAM_ACTION => Manager :: ACTION_SELECT_QUESTIONS,
                    \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID => Request :: get(
                        \Chamilo\Core\Repository\Viewer\Manager :: PARAM_ID)),
                Translation :: get('AddSelectedQuestions'),
                false));

        return $actions;
    }
}