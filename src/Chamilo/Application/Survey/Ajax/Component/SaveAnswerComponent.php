<?php
namespace Chamilo\Application\Survey\Ajax\Component;

use Chamilo\Application\Survey\Storage\DataClass\Answer;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class SaveAnswerComponent extends \Chamilo\Application\Survey\Ajax\Manager
{
    const PARAM_SURVEY_PUBLICATION_ID = 'survey_publication';
    const PARAM_ANSWER = 'answer';
    const PARAM_CONTEXT_PATH = 'context_path';
    const PARAM_SUCCES = 'succes';

    private $publication_id;
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    function getRequiredPostParameters()
    {
        return array(self :: PARAM_SURVEY_PUBLICATION_ID, self :: PARAM_ANSWER);
    }
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        var_dump($this);
        exit();
        
        $this->publication_id = $this->getPostDataValue(self :: PARAM_SURVEY_PUBLICATION_ID);
        
        $context_path = $this->getPostDataValue(self :: PARAM_CONTEXT_PATH);
        $ids = explode('_', $context_path);
        $complex_question_id = array_pop($ids);
        $answers = $this->getPostDataValue(self :: PARAM_ANSWER);
        
        if (count($answers) > 0)
        {
            
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Answer :: class_name(), Answer :: PROPERTY_USER_ID), 
                new StaticConditionVariable($this->get_user_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Answer :: class_name(), Answer :: PROPERTY_PUBLICATION_ID), 
                new StaticConditionVariable($this->publication_id));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Answer :: class_name(), Answer :: PROPERTY_COMPLEX_QUESTION_ID), 
                new StaticConditionVariable($complex_question_id));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Answer :: class_name(), Answer :: PROPERTY_CONTEXT_PATH), 
                new StaticConditionVariable($context_path));
            $condition = new AndCondition($conditions);
            $answer_object = DataManager :: retrieve(Answer :: CLASS_NAME, new DataClassRetrieveParameters($condition));
            
            if ($answer_object)
            {
                $answer_object->set_answer($answers);
                $answer_object->update();
            }
            else
            {
                $answer_object = new Answer();
                $answer_object->set_publication_id($this->publication_id);
                $answer_object->set_question_cid($complex_question_id);
                $answer_object->set_answer($answers);
                $answer_object->set_context_path($context_path);
                $answer_object->set_user_id($this->get_user_id());
                $answer_object->set_context_id(0);
                $answer_object->set_context_template_id(0);
                $succes = $answer_object->create();
            }
            
            if ($succes)
            {
                $result = new JsonAjaxResult(200);
                $result->display();
            }
            else
            {
                JsonAjaxResult :: general_error();
            }
        }
        else
        
        {
            $result = new JsonAjaxResult(204);
            $result->display();
        }
    }
}
?>