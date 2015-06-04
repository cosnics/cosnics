<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Configuration;

use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceInterface;

/**
 * @author Eduard.Vossen
 *
 */
class SurveyConfiguration extends ApplicationConfiguration
{
    

    /**
     * @var AnswerServiceInterface
     */
    private $answerService;
    
    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Chamilo\Core\User\Storage\DataClass\User $user $user
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceInterface $answerService
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user = null, $application = null,
        $answerService)
    {
        parent :: __construct($request, $user, $application);
        $this->answerService = $answerService;
    }
          
    /**
     * @return AnswerServiceInterface
     */
    public function getAnswerService()
    {
        return $this->answerService;
    }
   
}
