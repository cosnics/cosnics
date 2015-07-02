<?php
namespace Chamilo\Application\Survey\Ajax\Component;

use Chamilo\Application\Survey\Storage\DataClass\Answer;
use Chamilo\Application\Survey\Storage\DataClass\Participant;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

class DeleteAnswerComponent extends \Chamilo\Application\Survey\Ajax\Manager
{
    const PARAM_SURVEY_PUBLICATION_ID = 'survey_publication';
    const PARAM_CONTEXT_PATH = 'context_path';
    const PARAM_SUCCES = 'succes';

    private $publication_id;
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    function getRequiredPostParameters()
    {
        return array(self :: PARAM_SURVEY_PUBLICATION_ID, self :: PARAM_CONTEXT_PATH);
    }
    
    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    function run()
    {
        $this->publication_id = $this->getPostDataValue(self :: PARAM_SURVEY_PUBLICATION_ID);
        
        $context_path = $this->getPostDataValue(self :: PARAM_CONTEXT_PATH);
        $ids = explode('_', $context_path);
        $complex_question_id = array_pop($ids);
        
        $this->set_participant_tracker();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Answer :: class_name(), Answer :: PROPERTY_SURVEY_PARTICIPANT_ID),
            new StaticConditionVariable($this->participant_tracker->get_id()));
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
            $succes = $answer_object->delete();
        }
        else
        {
            $succes = false;
        }
        
        $result = new JsonAjaxResult(200);
        $result->display();
    }

    function set_participant_tracker()
    {
     $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Participant :: class_name(), Participant :: PROPERTY_SURVEY_PUBLICATION_ID), 
            new StaticConditionVariable($this->publication_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Participant :: class_name(), Participant :: PROPERTY_USER_ID), 
            new StaticConditionVariable($this->get_user_id()));
        $condition = new AndCondition($conditions);
        
        $count = DataManager :: count(Participant :: class_name(), new DataClassCountParameters($condition));
        
        if ($count == 0)
        {
            $this->participant = new Participant();
            $this->participant->set_survey_publication_id($this->publication_id);
            $this->participant->set_user_id($this->get_user_id());
            $this->participant->set_start_time(time());
            $this->participant->set_status(Participant :: STATUS_STARTED);
            $this->participant->set_context_template_id(0);
            $this->participant->set_total_time(time());
            $this->participant->set_context_id(0);
            $this->participant->create();
        }
        else
        {
            $this->participant = DataManager :: retrieve(
                Participant :: class_name(), 
                new DataClassRetrieveParameters($condition));
        }
    }
}
?>