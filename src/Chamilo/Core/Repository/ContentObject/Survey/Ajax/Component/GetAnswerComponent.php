<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Description\Storage\DataClass\ComplexDescription;
use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass\Survey;
use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;

class GetAnswerComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Ajax\Manager
{
    const TEMPORARY_STORAGE = 'survey_preview';
    const PARAM_COMPLEX_QUESTION_ID = 'complex_question_id';
    const PARAM_ANSWER = 'answer';
    const PARAM_PARAMETERS = 'parameters';
    const PARAM_RESULT = 'result';
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    function getRequiredPostParameters()
    {
        return array(self :: PARAM_PARAMETERS);
    }
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        $result = new JsonAjaxResult(200);
        
        $parameters = $this->getPostDataValue(self :: PARAM_PARAMETERS);
        
        $content_object_id = $parameters[Manager :: PARAM_CONTENT_OBJECT_ID];
        $step = $parameters[Manager :: PARAM_STEP];
        
        $content_object = DataManager :: retrieve_by_id(Survey :: class_name(), $content_object_id);
        
        $path = $content_object->get_complex_content_object_path();
        
        $page_node = $path->get_node($step);
        
        foreach ($page_node->get_children() as $node)
        {
            
            if (! $node->is_root())
            {
                
                $complex_content_object_item = $node->get_complex_content_object_item();
                
                if (! ($complex_content_object_item instanceof ComplexDescription))
                {
                    $complex_question_id = $complex_content_object_item->get_id();
                    $question_answers[$step][$complex_question_id] = $this->get_answer($complex_question_id);
                }
            }
        }
        
        $complex_question_id = $parameters[self :: PARAM_COMPLEX_QUESTION_ID];
        $answer = $question_answers[$step][$complex_question_id];
        
        $question_answer = array();
        $question_answer[self :: PARAM_COMPLEX_QUESTION_ID] = $complex_question_id;
        $question_answer[self :: PARAM_ANSWER] = $answer;
        
        $result->set_property(self :: PARAM_RESULT, $question_answer);
        $result->display();
    }

    private function get_answer($complex_question_id)
    {
        $answers = Session :: retrieve(self :: TEMPORARY_STORAGE);
               
        $answer = $answers[$complex_question_id];
        
        if ($answer)
        {
            return $answer;
        }
        else
        {
            return null;
        }
    }
}
?>