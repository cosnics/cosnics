<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\Wizard\AssessmentViewerWizardDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\Wizard\AssessmentViewerWizardProcess;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\Wizard\QuestionsAssessmentViewerWizardPage;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use HTML_QuickForm_Controller;

/**
 * $Id: assessment_viewer_wizard.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.viewer
 */
class AssessmentViewerWizard extends HTML_QuickForm_Controller
{

    private $parent;

    private $assessment;

    private $total_pages;

    public function __construct($parent, $assessment)
    {
        parent :: __construct('AssessmentViewerWizard_' . $parent->get_assessment_current_attempt_id(), true);
        
        $this->parent = $parent;
        $this->assessment = $assessment;
        
        $this->addpages();
        
        $this->addAction('process', new AssessmentViewerWizardProcess($this));
        $this->addAction('display', new AssessmentViewerWizardDisplay($this));
    }

    public function addpages()
    {
        $assessment = $this->assessment;
        if ($assessment->get_random_questions() == 0)
        {
            $total_questions = \Chamilo\Core\Repository\Storage\DataManager :: count_complex_content_object_items(
                ComplexContentObjectItem :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem :: class_name(), 
                        ComplexContentObjectItem :: PROPERTY_PARENT), 
                    new StaticConditionVariable($assessment->get_id())));
            Session :: register('questions', 'all');
        }
        else
        {
            $session_questions = Session :: retrieve('questions');
            
            if (! isset($session_questions) || $session_questions == 'all')
            {
                Session :: register('questions', $this->get_random_questions());
            }
            
            $total_questions = $assessment->get_random_questions();
        }
        
        $questions_per_page = $assessment->get_questions_per_page();
        
        if ($questions_per_page == 0)
        {
            $this->total_pages = 1;
        }
        else
        {
            $this->total_pages = ceil($total_questions / $questions_per_page);
        }
        
        for ($i = 1; $i <= $this->total_pages; $i ++)
        {
            $this->addPage(new QuestionsAssessmentViewerWizardPage('question_page_' . $i, $this, $i));
        }
    }

    public function get_questions($page_number)
    {
        $assessment = $this->assessment;
        $questions_per_page = $this->assessment->get_questions_per_page();
        
        $session_questions = Session :: retrieve('questions');
        
        if ($session_questions == 'all')
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(), 
                    ComplexContentObjectItem :: PROPERTY_PARENT), 
                new StaticConditionVariable($assessment->get_id()), 
                ComplexContentObjectItem :: get_table_name());
        }
        else
        {
            $condition = new InCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(), 
                    ComplexContentObjectItem :: PROPERTY_ID), 
                $session_questions, 
                ComplexContentObjectItem :: get_table_name());
        }
        
        if ($questions_per_page == 0)
        {
            $start = null;
            $stop = null;
        }
        else
        {
            $start = (($page_number - 1) * $questions_per_page);
            $stop = $questions_per_page;
        }
        $parameters = new DataClassRetrievesParameters($condition, $stop, $start);
        
        $questions = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(), 
            $parameters);
        return $questions;
    }

    public function get_random_questions()
    {
        $questions = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(), 
            new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(), 
                    ComplexContentObjectItem :: PROPERTY_PARENT), 
                new StaticConditionVariable($this->assessment->get_id()), 
                ComplexContentObjectItem :: get_table_name()));
        while ($question = $questions->next_result())
        {
            $question_list[] = $question;
        }
        
        $random_questions = array();
        
        $number_of_random_questions = $this->assessment->get_random_questions();
        if (count($question_list) < $number_of_random_questions)
        {
            foreach ($question_list as $question)
            {
                $random_questions[] = $question->get_id();
            }
        }
        else
        {
            $random_keys = array_rand($question_list, $this->assessment->get_random_questions());
            
            if (! is_array($random_keys))
            {
                $random_keys = array($random_keys);
            }
            
            foreach ($random_keys as $random_key)
            {
                $random_questions[] = $question_list[$random_key]->get_id();
            }
        }
        
        return $random_questions;
    }

    public function get_parent()
    {
        return $this->parent;
    }

    public function get_assessment()
    {
        return $this->assessment;
    }

    public function get_total_pages()
    {
        return $this->total_pages;
    }
}
