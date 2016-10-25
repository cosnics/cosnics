<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Configuration;

use Chamilo\Core\Repository\ContentObject\Survey\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceFactory;
use Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 *
 * @author Eduard.Vossen
 */
class SurveyConfiguration extends ApplicationConfiguration
{

    /**
     *
     * @var AnswerServiceInterface
     */
    private $answerServiceContext;

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Chamilo\Core\User\Storage\DataClass\User $user $user
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param string $answerServiceContext
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user = null, $application = null, 
        $answerServiceContext = null)
    {
        parent :: __construct($request, $user, $application);
        $this->answerServiceContext = $answerServiceContext;
        $this->set(Manager :: ANSWER_SERVICE_KEY, $this->getAnswerService());
    }

    /**
     *
     * @param array $parameters
     * @return AnswerServiceInterface
     */
    public function getAnswerService($parameters = null)
    {
        if ($this->answerServiceContext)
        {
            $answerServiceFactory = new AnswerServiceFactory($this->answerServiceContext, $parameters);
        }
        else
        {
            $answerServiceFactory = new AnswerServiceFactory($this->getApplication()->package(), $parameters);
            
        }
        return $answerServiceFactory->getAnswerService();
    }
}
