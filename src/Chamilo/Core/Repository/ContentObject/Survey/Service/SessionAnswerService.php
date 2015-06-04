<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Service;

use Chamilo\Libraries\Platform\Session\Session;

class SessionAnswerService implements AnswerServiceInterface
{
    const TEMPORARY_STORAGE = 'surveyTempSessionStorage';
    const PARAM_QUESTION_ID = 'questionId';

    public function saveAnswer($questionId, $answer)
    {
        if (! Session :: retrieve(self :: TEMPORARY_STORAGE))
        {
            Session :: register(self :: TEMPORARY_STORAGE, array());
        }
        
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        $answers[$questionId] = $answer;
        Session :: register(self :: TEMPORARY_STORAGE, $answers);
    }

    public function getAnswer($questionId)
    {
        if (! Session :: retrieve(self :: TEMPORARY_STORAGE))
        {
            Session :: register(self :: TEMPORARY_STORAGE, array());
        }
        
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        $answer = $answers[$questionId];
        
        if ($answer)
        {
            return $answer;
        }
        else
        {
            return null;
        }
    }
    
    public function deleteAnswer($questionId)
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
        unset($answers[$questionId]);
        Session :: register(self :: TEMPORARY_STORAGE, $answers);
    }
    
    public function getPrefix()
    {
        return 'session';
    }
    
}