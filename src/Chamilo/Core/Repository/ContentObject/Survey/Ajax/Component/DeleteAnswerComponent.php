<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Ajax\Component;

use Chamilo\Libraries\Architecture\JsonAjaxResult;

class DeleteAnswerComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Ajax\Manager
{

    function run()
    {
        $result = new JsonAjaxResult(200);
        $nodeId = $this->getRequest()->request->get(self :: PARAM_NODE_ID);
        $answerId = $this->getRequest()->request->get(self :: PARAM_ANSWER_ID);
        $this->deleteAnswer($nodeId, $answerId);
        $result->display();
    }

    function deleteAnswer($nodeId, $answerId)
    {
        $answerService = $this->getApplicationConfiguration()->getAnswerService();
        if ($answerId)
        {
            $answer = $answerService->getAnswer($nodeId);
            unset($answer[$answerId]);
            $answerService->saveAnswer($nodeId, $answer);
        }
        else
        {
            $answerService->deleteAnswer($nodeId);
        }
    }
}
?>