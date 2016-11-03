<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;

class DeleteAnswerComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Ajax\Manager
{
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
        $complex_question_id = $parameters[self :: PARAM_COMPLEX_QUESTION_ID];
        $answer = $parameters[self :: PARAM_ANSWER];
        $this->delete_answer($complex_question_id, $answer);
        
        $result->display();
    }

    function delete_answer($complex_question_id, $answer)
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        $answer_id = $answer[self :: PARAM_ANSWER_ID];
        
        if ($answer_id)
        {
            unset($answers[$complex_question_id][$answer_id]);
        }
        else
        {
            unset($answers[$complex_question_id]);
        }
        
        Session :: register(self :: TEMPORARY_STORAGE, $answers);
    }
}
?>