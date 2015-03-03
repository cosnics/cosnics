<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Answer;
use Chamilo\Application\Survey\Storage\DataClass\Participant;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Survey\Storage\DataClass\Survey;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class TakerComponent extends Manager implements DelegateComponent

{

    private $survey;

    private $publication_id;

    private $publication;

    /**
     *
     * @var Participant
     */
    private $participant;

    function run()
    {
        $this->publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);
        
        if (! Rights :: get_instance()->is_right_granted(Rights :: PARTICIPATE_RIGHT, $this->publication_id))
        {
            Display :: not_allowed();
        }
        
        $this->publication = DataManager :: retrieve_by_id(Publication :: class_name(), $this->publication_id);
        
        if (! $this->publication->is_publication_period())
        {
            $this->redirect(
                Translation :: get('NotInPublicationPeriod'), 
                (false), 
                array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        
        $this->survey = $this->publication->get_publication_object();
        
        $this->started();
        \Chamilo\Core\Repository\Display\Manager :: launch(Survey :: class_name(), $this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_BROWSE, 
                        BrowserComponent :: PARAM_TABLE_TYPE => BrowserComponent :: TAB_PARTICIPATE)), 
                Translation :: get('BrowserComponent')));
    }

    function get_parameters()
    {
        return array(
            self :: PARAM_PUBLICATION_ID, 
            \Chamilo\Core\Repository\ContentObject\Survey\Display\Component\ViewerComponent :: PARAM_STEP);
    }
    
    // try out for interface SurveyTaker
    function started()
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

    function finished($progress)
    {
        $this->participant->set_progress($progress);
        $this->participant->set_status(Participant :: STATUS_FINISHED);
        $this->participant->set_total_time(time());
        $this->participant_tracker->update();
    }

    function save_answer($complex_question_id, $answer, $context_path)
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
            $answer_object->set_answer($answer);
            $answer_object->update();
        }
        else
        {
            $answer_object = new Answer();
            $answer_object->set_publication_id($this->publication_id);
            $answer_object->set_question_cid($complex_question_id);
            $answer_object->set_answer($answer);
            $answer_object->set_context_path($context_path);
            $answer_object->set_user_id($this->get_user_id());
            $answer_object->set_context_id(0);
            $answer_object->set_context_template_id(0);
            $answer_object->create();
        }
    }

    function get_answer($complex_question_id, $context_path)
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
            $answer_object->get_answer();
        }
        else
        {
            return null;
        }
    }

    function get_publication_id()
    {
        return $this->publication->get_id();
    }

    function get_go_back_url()
    {
        return $this->get_browse_survey_publications_url();
    }

    function get_root_content_object()
    {
        return $this->survey;
    }
}
?>