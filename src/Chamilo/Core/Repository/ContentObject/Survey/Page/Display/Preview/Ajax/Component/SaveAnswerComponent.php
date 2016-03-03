<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;

class SaveAnswerComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Ajax\Manager
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
        $complexQuestionId = $parameters[self :: PARAM_COMPLEX_QUESTION_ID];
        $answer = $parameters[self :: PARAM_ANSWER];
        $this->saveAnswer($complexQuestionId, $answer);
        
        $result->display();
    }

    function saveAnswer($complexQuestionId, $answer)
    {
        $answerService = $this->getApplicationConfiguration()->getAnswerService();
        $answerService->saveAnswer($complexQuestionId, $answer);
    }
}
?>