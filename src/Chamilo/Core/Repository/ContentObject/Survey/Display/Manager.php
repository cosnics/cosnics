<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Display\Interfaces\SurveyDisplaySupport;

abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const PARAM_AJAX_CONTEXT = 'ajax_context';
    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';
    const PARAM_ROOT_CONTENT_OBJECT_ID = 'root_content_object_id';
    const PARAM_STEP = 'step';
    
    
    public function __construct(\Symfony\Component\HttpFoundation\Request $request, $user, $application)
    {
   
        if (! $application instanceof SurveyDisplaySupport)
        {
            throw new \Exception(
                get_class($application) .
                     ' uses the SurveyDisplaySupport, please implement the SurveyDisplaySupport interface');
        }
        
        parent :: __construct($request, $user, $application);
        
    }
    
}
?>