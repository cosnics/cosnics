<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component\AssessmentMerger;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class ObjectTable extends DataClassTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_QUESTION_ID;

    public function getTableActions(): TableFormActions
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_SELECT_QUESTIONS, 
                        \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID => Request::get(
                            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ID))), 
                Translation::get('AddSelectedQuestions'), 
                false));
        
        return $actions;
    }
}