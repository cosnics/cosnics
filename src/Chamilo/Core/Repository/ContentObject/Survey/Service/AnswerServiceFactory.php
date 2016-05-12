<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Service;

use Chamilo\Libraries\Architecture\Exceptions\ClassNotExistException;

class AnswerServiceFactory
{

    private $context;

    private $parameters;

    /**
     * @param string $context
     * @param array $parameters
     */
    function __construct($context, $parameters = null)
    {
        $this->context = $context;
        $this->parameters = $parameters;
    }

    /**
     * @throws ClassNotExistException
     * @return \Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceInterface
     */
    function getAnswerService()
    {
        $class = $this->context . '\Integration\Chamilo\Core\Repository\ContentObject\Survey\AnswerService';
        
        if (class_exists($class))
        {
            if ($this->parameters)
            {
                $answerService = new $class($this->parameters);
            }
            else
            {
                $answerService = new $class();
            }
        }
        else
        {
            throw new ClassNotExistException($class);
        }
        
        return $answerService;
    }
}