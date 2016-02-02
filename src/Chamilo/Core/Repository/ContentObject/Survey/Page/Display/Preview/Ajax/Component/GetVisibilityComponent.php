<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package repository.content_object.survey;
 */
class GetVisibilityComponent extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Display\Preview\Ajax\Manager
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
        $parameters = $this->getPostDataValue(self :: PARAM_PARAMETERS);
        $content_object_id = $parameters['content_object_id'];
        
        $content_object = DataManager :: retrieve_by_id(Page :: class_name(), $content_object_id);
        
        $path = $content_object->get_complex_content_object_path();
        
        $question_visibility = array();
        $question_answers = array();
        
        foreach ($path->get_nodes() as $node)
        {
            
            if (! $node->is_root())
            {
                
                $complex_content_object_item = $node->get_complex_content_object_item();
                $complex_question_id = $complex_content_object_item->get_id();
                
                if ($complex_content_object_item->is_visible())
                {
                    
                    $question_visibility[$complex_question_id] = true;
                }
                else
                {
                    $question_visibility[$complex_question_id] = false;
                }
                
                $answer = $this->get_answer($complex_question_id);
                
                if ($answer)
                {
                    $question_answers[$complex_question_id] = $answer;
                }
            }
        }
        
        if (count($question_answers) > 0)
        {
            $configs = $content_object->getConfiguration();
            
            foreach ($question_answers as $complex_question_id => $answers)
            {
                foreach ($configs as $config)
                {
                    $from_question_id = $config->getComplexQuestionId();
                    
                    if ($complex_question_id == $from_question_id)
                    {
                        $answer_matches = $config->getAnswerMatches();
                        
                        $visible = false;
                        if (count($answer_matches) == count($answers))
                        {
                            foreach ($answer_matches as $key => $value)
                            {
                                
                                if (array_key_exists($key, $answers))
                                {
                                    if ($value == $answers[$key])
                                    {
                                        $visible = true;
                                    }
                                    else
                                    {
                                        $visible = false;
                                        break;
                                    }
                                }
                                else
                                {
                                    $visible = false;
                                    break;
                                }
                            }
                        }
                        
                        if ($visible)
                        {
                            foreach ($config->getToVisibleQuestionIds() as $id)
                            {
                                $question_visibility[$id] = true;
                            }
                        }
                    }
                }
            }
            
            $result = new JsonAjaxResult(200);
            $result->set_property(self :: PARAM_QUESTION_VISIBILITY, $question_visibility);
            $result->display();
        }
        else
        {
            $result = new JsonAjaxResult(200);
            $result->set_property(self :: PARAM_QUESTION_VISIBILITY, array());
            $result->display();
        }
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