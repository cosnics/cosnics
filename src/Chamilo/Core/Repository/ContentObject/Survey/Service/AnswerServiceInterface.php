<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Service;

interface AnswerServiceInterface
{
    
    /**
     *
     * @var string
     */
    const PARAM_SERVICE_CONTEXT = 'answerServiceContext';

    /**
     *
     * @param int $nodeId
     * @return mixed $answer
     */
    public function getAnswer($nodeId);

    /**
     *
     * @param int $nodeId
     * @param mixed $answer
     */
    public function saveAnswer($nodeId, $answer);

    /**
     *
     * @param int $nodeId
     * @return boolean $succes
     */
    public function deleteAnswer($nodeId);

    /**
     *
     * @return string
     */
    public function getPrefix();

    /**
     *
     * @return string
     */
    public function getServiceContext();
}