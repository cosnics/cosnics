<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Table\AnswerFeedbackType;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Configuration;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;

/**
 *
 * @package core\repository\content_object\assessment\builder
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AnswerFeedbackTypeTable extends DataClassTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_COMPLEX_QUESTION_ID;

    /**
     *
     * @see \libraries\format\TableFormActionsSupport::get_implemented_form_actions()
     */
    public function getTableActions(): TableFormActions
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        
        $types = array(
            Configuration::ANSWER_FEEDBACK_TYPE_NONE, 
            Configuration::ANSWER_FEEDBACK_TYPE_GIVEN, 
            Configuration::ANSWER_FEEDBACK_TYPE_GIVEN_CORRECT, 
            Configuration::ANSWER_FEEDBACK_TYPE_GIVEN_WRONG, 
            Configuration::ANSWER_FEEDBACK_TYPE_CORRECT, 
            Configuration::ANSWER_FEEDBACK_TYPE_WRONG, 
            Configuration::ANSWER_FEEDBACK_TYPE_ALL);
        
        foreach ($types as $type)
        {
            $actions->add_form_action(
                new TableFormAction(
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_ANSWER_FEEDBACK_TYPE, 
                            Manager::PARAM_ANSWER_FEEDBACK_TYPE => $type)), 
                    Configuration::answer_feedback_string($type)));
        }
        
        return $actions;
    }
}
