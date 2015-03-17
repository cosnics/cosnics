<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display\Preview\Ajax\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;

class DeleteAnswerComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Display\Preview\Ajax\Manager
{
    const TEMPORARY_STORAGE = 'survey_preview';
    const PARAM_COMPLEX_QUESTION_ID = 'complex_question_id';
    const PARAM_ANSWER = 'answer';
    const PARAM_ANSWER_ID = 'answer_id';
    const PARAM_ANSWER_VALUE = 'answer_value';
    const PARAM_PARAMETERS = 'parameters';
    const PARAM_RESULT = 'result';
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    function getRequiredPostParameters()
    {
        return array(self :: PARAM_PARAMETERS);
    }
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        $result = new JsonAjaxResult(200);
        
        $parameters = $this->getPostDataValue(self :: PARAM_PARAMETERS);
        
        $step = $parameters[Manager :: PARAM_STEP];
        $complex_question_id = $parameters[self :: PARAM_COMPLEX_QUESTION_ID];
        $answer = $parameters[self :: PARAM_ANSWER];
        $this->delete_answer($complex_question_id, $answer);
        
        $result->display();
    }

    function delete_answer($step, $complex_question_id, $answer)
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        $answer_id = $answer[self :: PARAM_ANSWER_ID];
        unset($answers[$step][$complex_question_id][$answer_id]);
        Session :: register(self :: TEMPORARY_STORAGE, $answers);
    }
}
?>