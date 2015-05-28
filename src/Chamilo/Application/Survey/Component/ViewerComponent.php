<?php
namespace Chamilo\Application\Survey\Component;

use Chamilo\Application\Survey\Manager;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Answer;
use Chamilo\Application\Survey\Storage\DataClass\Participant;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Survey\Display\Interfaces\SurveyDisplaySupport;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

class ViewerComponent extends Manager implements DelegateComponent, SurveyDisplaySupport
{

    private $survey_id;

    private $survey;

    private $publication_id;

    private $invitee_id;

    private $publication;

    /**
     *
     * @var Participant
     */
    private $participant;

    function run()
    {
        $this->publication_id = Request :: get(self :: PARAM_PUBLICATION_ID);

        $this->invitee_id = Request :: get(self :: PARAM_INVITEE_ID);

        if (! Rights :: get_instance()->is_right_granted(
            Rights :: PARTICIPATE_RIGHT,
            $this->publication_id,
            $this->invitee_id))
        {
            throw new NotAllowedException(false);
        }

        $this->publication = DataManager :: retrieve_by_id(Publication :: class_name(), $this->publication_id);
        $this->survey = $this->publication->get_publication_object();
        
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\ContentObject\Survey\Display\Manager :: context(),
           new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));

        $component = $factory->getComponent();
        $component->set_parameter(self :: PARAM_PUBLICATION_ID,  $this->publication_id);
        
        return $component->run();    }


    function started()
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Participant :: class_name(), Participant :: PROPERTY_SURVEY_PUBLICATION_ID),
            new StaticConditionVariable($this->publication_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Participant :: class_name(), Participant :: PROPERTY_USER_ID),
            new StaticConditionVariable($this->invitee_id));
        $condition = new AndCondition($conditions);

        $count = DataManager :: count(Participant :: class_name(), new DataClassCountParameters($condition));

        if ($count == 0)
        {
            $this->participant = new Participant();
            $this->participant->set_survey_publication_id($this->publication_id);
            $this->participant->set_user_id($this->invitee_id);
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

    function finished($progrees)
    {
        // status will not be updated in viewer
    }

    function save_answer($complex_question_id, $answer, $context_path)
    {
        // answers will not be saved in the viewer
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
        return $this->publication_id;
    }

    function get_invitee_id()
    {
        return $this->invitee_id;
    }

    function get_go_back_url()
    {
        return $this->get_browse_survey_publications_url();
    }

    function get_root_content_object()
    {
        return $this->survey;
    }

    /*
     * (non-PHPdoc) @see \core\repository\display\DisplaySupport::is_allowed()
     */
    public function is_allowed($right)
    {
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc) @see \core\repository\display\DisplaySupport::is_allowed_to_view_content_object()
     */
    public function is_allowed_to_view_content_object()
    {
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc) @see \core\repository\display\DisplaySupport::is_allowed_to_edit_content_object()
     */
    public function is_allowed_to_edit_content_object()
    {
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc) @see \core\repository\display\DisplaySupport::is_allowed_to_add_child()
     */
    public function is_allowed_to_add_child()
    {
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc) @see \core\repository\display\DisplaySupport::is_allowed_to_delete_child()
     */
    public function is_allowed_to_delete_child()
    {
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc) @see \core\repository\display\DisplaySupport::is_allowed_to_delete_feedback()
     */
    public function is_allowed_to_delete_feedback()
    {
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc) @see \core\repository\display\DisplaySupport::is_allowed_to_edit_feedback()
     */
    public function is_allowed_to_edit_feedback()
    {
        // TODO Auto-generated method stub
    }
}

?>