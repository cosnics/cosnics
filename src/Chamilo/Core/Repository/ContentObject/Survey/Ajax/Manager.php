<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Ajax;

use Chamilo\Core\Repository\ContentObject\Survey\Configuration\SurveyConfiguration;
use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    
    const PARAM_NODE_ID = 'node_id';
    const PARAM_CONTENT_OBJECT_ID= 'content_object_id';
    const PARAM_ANSWER_ID = 'answer_id';
    const PARAM_ANSWER_VALUE = 'answer_value';
    const PARAM_QUESTION_VISIBILITY = 'question_visibility';
       
    /**
     * @param SurveyConfiguration $surveyConfiguration
     */
    public function __construct(SurveyConfiguration $surveyConfiguration)
    {
        parent :: __construct($surveyConfiguration);
    }
}
