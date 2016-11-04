<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Configuration\SurveyConfiguration;
use Chamilo\Core\Repository\ContentObject\Survey\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceInterface;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;

class AjaxComponent extends Manager implements DelegateComponent
{

    function run()
    {
        
//         $parameters = $this->getRequest()->request->get('parameters');
        $context = $this->getRequest()->request->get(AnswerServiceInterface :: PARAM_SERVICE_CONTEXT);
        
        $surveyConfiguration = new SurveyConfiguration(
            $this->getRequest(), 
            $this->get_user(), 
            $this, 
            $context);
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\ContentObject\Survey\Ajax\Manager :: context(), 
            $surveyConfiguration);
//         $component = $factory->getComponent();
//         $component->set_p; 
        return $factory->run();
    }
}