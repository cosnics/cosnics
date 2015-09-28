<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;

class GetAnswerComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Ajax\Manager
{
    
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
        $content_object_id = $parameters['content_object_id'];
        
        $content_object = DataManager :: retrieve_by_id(Page :: class_name(), $content_object_id);
        
        $path = $content_object->get_complex_content_object_path();
        
        $question_answers = array();
        
        foreach ($path->get_nodes() as $node)
        {
            
            if (! $node->is_root())
            {
                if ($node->is_question())
                {
                    $complex_content_object_item = $node->get_complex_content_object_item();
                    $complex_question_id = $complex_content_object_item->get_id();
                    $question_answers[$complex_question_id] = $this->get_answer($complex_question_id);
                }
            }
        }
        
        $complex_question_id = $parameters[self :: PARAM_COMPLEX_QUESTION_ID];
        $answer = $question_answers[$complex_question_id];
        
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