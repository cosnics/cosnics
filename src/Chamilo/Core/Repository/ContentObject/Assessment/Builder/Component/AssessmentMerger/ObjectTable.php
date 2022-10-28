<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component\AssessmentMerger;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class ObjectTable extends DataClassListTableRenderer implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_QUESTION_ID;

    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->addAction(
            new TableAction(
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