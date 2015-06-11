<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;

class SaveAnswerComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Ajax\Manager
{
    function run()
    {
        $result = new JsonAjaxResult(200);
        $nodeId = $this->getRequest()->request->get(self :: PARAM_NODE_ID);
        $complexQuestionId = $this->getRequest()->request->get(self :: PARAM_COMPLEX_QUESTION_ID);
        $answerId = $this->getRequest()->request->get(self :: PARAM_ANSWER_ID);
        $answerValue = $this->getRequest()->request->get(self :: PARAM_ANSWER_VALUE);
        $this->saveAnswer($nodeId, $complexQuestionId, $answerId, $answerValue);
        $result->display();
    }

    private function saveAnswer($nodeId, $complexQuestionId, $answerId, $answerValue)
    {
        $answerService = $this->getApplicationConfiguration()->getAnswerService();
        $answers = $answerService->getAnswer($nodeId);
        $answers[$answerId] = $answerValue;
        $answerService->saveAnswer($nodeId, $answers);
    }
}
?>