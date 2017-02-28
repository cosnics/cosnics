<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\AssessmentViewerComponent;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\ScoreCalculator;
use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Platform\Translation;

class AssessmentResultProcessor
{

    /**
     *
     * @var AssessmentViewerComponent
     */
    private $assessment_viewer;

    /**
     * To determine wheter it is a result in between or completely finished
     * 
     * @var boolean
     */
    private $finished;

    /**
     *
     * @var array
     */
    private $question_results = array();

    public function __construct(AssessmentViewerComponent $assessment_viewer)
    {
        $this->assessment_viewer = $assessment_viewer;
    }

    public function get_page_number()
    {
        return $this->get_assessment_viewer()->get_questions_page();
    }

    public function save_answers($results_page_number)
    {
        $questions_cloi = $this->assessment_viewer->get_questions_for_page($results_page_number);
        
        if ($this->assessment_viewer->get_root_content_object()->get_questions_per_page() == 0)
        {
            $question_number = 1;
        }
        else
        {
            $question_number = (($results_page_number - 1) *
                 $this->assessment_viewer->get_root_content_object()->get_questions_per_page()) + 1;
        }
        
        $values = $_POST;
        
        foreach ($values as $key => $value)
        {
            $value = Security::remove_XSS($value);
            
            if (! is_array($value))
            {
                $value = trim($value);
            }
            else
            {
                foreach ($value as &$value_element)
                {
                    $value_element = trim($value_element);
                }
            }
            
            $split_key = explode('_', $key);
            $question_id = $split_key[0];
            
            if (is_numeric($question_id))
            {
                $answer_index = $split_key[1];
                $values[$question_id][$answer_index] = $value;
            }
        }
        
        foreach ($questions_cloi as $question_cloi)
        {
            $answers = $values[$question_cloi->get_id()];
            $hints = $values['hint_question'][$question_cloi->get_id()];
            
            $score_calculator = ScoreCalculator::factory(
                $question_cloi->get_ref_object(), 
                $answers, 
                $question_cloi->get_weight());
            $score = $score_calculator->calculate_score();
            
            if ($this->assessment_viewer->showFeedbackAfterEveryPage())
            {
                $display = AssessmentQuestionResultDisplay::factory(
                    $this->get_assessment_viewer(), 
                    $question_cloi, 
                    $question_number, 
                    $answers, 
                    $score, 
                    $hints);
                
                $this->question_results[] = $display->as_html();
            }
            
            $question_number ++;
            
            // $tracker = $this->assessment_viewer->get_assessment_question_attempt($question_cloi->get_id());
            //
            // if (is_null($tracker))
            // {
            $this->assessment_viewer->save_assessment_answer(
                $question_cloi->get_id(), 
                serialize($answers), 
                $score, 
                $values['hint_question'][$question_cloi->get_id()]);
            // }
            // elseif (! is_null($tracker) && !
            // $this->assessment_viewer->get_configuration()->show_feedback_after_every_page())
            // {
            // $tracker->set_answer(serialize($answers));
            // $tracker->set_score($score);
            // $tracker->set_hint($values['hint_question'][$question_cloi->get_id()]);
            // $tracker->update();
            // }
        }
    }

    public function finish_assessment()
    {
        $assessment = $this->get_assessment_viewer()->get_root_content_object();
        $this->finished = true;
        
        $this->question_results[] = '<div class="assessment">';
        $this->question_results[] = '<h2>' . Translation::get('ViewAssessmentResults') . '</h2>';
        
        $this->question_results[] = '<div class="form-row"><div class="formc formc_no_margin">';
        $this->question_results[] = '<b>' . $assessment->get_title() . '</b><br />';
        $this->question_results[] = $assessment->get_description() . '</div></div>';
        
        $this->question_results[] = '</div>';
        
        $complex_questions = $this->get_assessment_viewer()->get_questions();
        
        $answers = $this->get_assessment_viewer()->get_assessment_question_attempts();
        
        $question_number = 1;
        $total_score = 0;
        $total_weight = 0;
        
        foreach ($complex_questions as $complex_question)
        {
            $tracker = $answers[$complex_question->get_id()];
            if ($tracker)
            {
                $answer = unserialize($tracker->get_answer());
                $score = $tracker->get_score();
            }
            else
            {
                $score = 0;
                $answer = null;
            }
            
            $total_score += $score;
            $total_weight += $complex_question->get_weight();
            
            $question_number ++;
        }
        
        if ($total_score < 0)
        {
            $total_score = 0;
        }
        
        $percent = round(($total_score / $total_weight) * 100);
        $this->get_assessment_viewer()->save_assessment_result($percent);
        
        if ($this->get_assessment_viewer()->get_configuration()->show_score())
        {
            $this->question_results[] = '<div class="panel panel-default">';

            $this->question_results[] = '<div class="panel-heading">';
            $this->question_results[] = '<h3 class="panel-title pull-left">' . Translation::get('TotalScore') . '</h3>';
            $this->question_results[] = '<div class="pull-right">';

            $this->question_results[] = $total_score . ' / ' . $total_weight . ' (' . $percent . '%)';

            $this->question_results[] = '</div>';
            $this->question_results[] = '<div class="clearfix"></div>';
            $this->question_results[] = '</div>';
            $this->question_results[] = '</div>';

        }
        
        $question_number = 1;
        
        foreach ($complex_questions as $complex_question)
        {
            $tracker = $answers[$complex_question->get_id()];
            if ($tracker)
            {
                $answer = unserialize($tracker->get_answer());
                $hints = $tracker->get_hint();
                $score = $tracker->get_score();
            }
            else
            {
                $score = 0;
                $hints = 0;
                $answer = null;
            }
            
            $display = AssessmentQuestionResultDisplay::factory(
                $this->get_assessment_viewer(), 
                $complex_question, 
                $question_number, 
                $answer, 
                $score, 
                $hints);
            
            $this->question_results[] = $display->as_html();
            
            $question_number ++;
        }
    }

    /**
     *
     * @return array
     */
    public function get_question_results()
    {
        return $this->question_results;
    }

    /**
     *
     * @return AssessmentViewerComponent
     */
    public function get_assessment_viewer()
    {
        return $this->assessment_viewer;
    }

    /**
     * Returns wheter the result is finished or not
     * 
     * @return boolean
     */
    public function is_finished()
    {
        return $this->finished;
    }

    /**
     *
     * @param boolean $finished
     */
    public function set_finished($finished)
    {
        $this->finished = $finished;
    }

    /**
     *
     * @return string
     */
    public function get_results()
    {
        $form = new AssessmentResultViewerForm($this, 'post', $this->assessment_viewer->get_url());
        return $form->toHtml();
    }
}
