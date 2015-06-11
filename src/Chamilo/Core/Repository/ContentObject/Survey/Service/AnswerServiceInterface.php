<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Service;

interface AnswerServiceInterface
{
    

    /**
     * @var string
     */
    const PARAM_SERVICE_CONTEXT = 'answerServiceContext';
    
    /**
     * @param int $questionId
     * @return mixed $answer
     */
    public function getAnswer($questionId);
    
    /**
     * @param int $questionId
     * @param mixed $answer
     */
    public function saveAnswer($questionId, $answer);
    
    /**
     * @param int $questionId
     * @return boolean $succes
     */
    public function deleteAnswer($questionId);
    
    /**
     * @return string
     */
    public function getPrefix();
    
    /**
     * @return string
     */
    public function getServiceContext();
    
}