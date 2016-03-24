<?php
namespace Chamilo\Application\Survey\Integration\Chamilo\Core\Repository\ContentObject\Survey;

use Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceInterface;
use Chamilo\Libraries\Platform\Session\Session;

class AnswerService implements AnswerServiceInterface
{
    const TEMPORARY_STORAGE = 'surveyTempSessionStorage';
//     const PARAM_QUESTION_ID = 'questionId';

    public function saveAnswer($nodeId, $answer)
    {
        if (! Session :: retrieve(self :: TEMPORARY_STORAGE))
        {
            Session :: register(self :: TEMPORARY_STORAGE, array());
        }
        
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        $answers[$nodeId] = $answer;
        Session :: register(self :: TEMPORARY_STORAGE, $answers);
    }

    public function getAnswer($nodeId)
    {
        if (! Session :: retrieve(self :: TEMPORARY_STORAGE))
        {
            Session :: register(self :: TEMPORARY_STORAGE, array());
        }
        
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        $answer = $answers[$nodeId];
        
        if ($answer)
        {
            return $answer;
        }
        else
        {
            return null;
        }
    }
    
    public function deleteAnswer($nodeId)
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        unset($answers[$nodeId]);
        Session :: register(self :: TEMPORARY_STORAGE, $answers);
    }
    
    public function getPrefix()
    {
        return 'session';
    }
   
    public function getServiceContext()
    {
        return 'Chamilo\Application\Survey';
    }
}