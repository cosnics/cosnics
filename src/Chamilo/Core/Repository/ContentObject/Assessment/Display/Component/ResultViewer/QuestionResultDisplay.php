<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentQuestionResultDisplay;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.result_viewer
 */
class QuestionResultDisplay
{

    private $complex_content_object_question;

    private $question;

    private $question_nr;

    private $answers;

    private $score;

    private $hints;

    private $feedback;

    private $form;

    private $results_viewer;

    private $can_change;

    public function __construct($results_viewer, &$form, $complex_content_object_question, $question_nr, $answers, 
        $score, $hints, $feedback, $can_change)
    {
        $this->complex_content_object_question = $complex_content_object_question;
        $this->question_nr = $question_nr;
        $this->question = $complex_content_object_question->get_ref_object();
        $this->answers = $answers;
        $this->score = $score;
        $this->hints = $hints;
        $this->feedback = $feedback;
        $this->form = $form;
        $this->results_viewer = $results_viewer;
        $this->can_change = $can_change;
    }

    public function get_complex_content_object_question()
    {
        return $this->complex_content_object_question;
    }

    public function get_question()
    {
        return $this->question;
    }

    public function get_question_nr()
    {
        return $this->question_nr;
    }

    public function get_answers()
    {
        return $this->answers;
    }

    public function get_score()
    {
        return $this->score;
    }

    public function get_feedback()
    {
        return $this->feedback;
    }

    public function get_results_viewer()
    {
        return $this->results_viewer;
    }

    public function can_change()
    {
        return $this->can_change;
    }

    public function render()
    {
        $this->render_header();
        
        if ($this->add_borders())
        {
            $header = array();
            $header[] = '<div class="with_borders">';
            
            $this->form->addElement('html', implode(PHP_EOL, $header));
        }
        
        $display = AssessmentQuestionResultDisplay::factory(
            $this->results_viewer, 
            $this->complex_content_object_question, 
            $this->question_nr, 
            $this->answers, 
            $this->score, 
            $this->hints);
        
        $this->form->addElement('html', $display->get_question_result());
        
        if ($this->add_borders())
        {
            $footer = array();
            $footer[] = '<div class="clear"></div>';
            $footer[] = '</div>';
            $this->form->addElement('html', implode(PHP_EOL, $footer));
        }
        
        $this->display_feedback();
        
        $this->form->addElement('html', $this->render_footer());
    }

    public function render_header()
    {
        $html = array();
        
        $html[] = '<div class="question">';
        $html[] = '<div class="title">';
        $html[] = '<div class="number">';
        $html[] = '<div class="bevel">';
        $html[] = $this->question_nr . '.';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="text">';
        
        $html[] = '<div class="bevel" style="float: left;">';
        $html[] = $this->question->get_title();
        $html[] = '</div>';
        $html[] = '<div class="bevel" style="text-align: right;">';
        $this->form->addElement('html', implode(PHP_EOL, $html));
        $html = array();
        
        if ($this->hints > 0)
        {
            $variable = $this->hints == 1 ? 'HintUsed' : 'HintsUsed';
            $label = Translation::get($variable, array('COUNT' => $this->hints));
            
            $html[] = '<img style="float: none; vertical-align: baseline;" src="' . Theme::getInstance()->getImagePath(
                'Chamilo\Core\Repository\ContentObject\Assessment\Display', 
                'Buttons/ButtonHint') . '" alt="' . $label . '" title="' . htmlentities($label) . '" />&nbsp;&nbsp;';
        }
        
        if (! $this->can_change)
        {
            if ($this->get_results_viewer()->get_configuration()->show_score())
            {
                $html[] = $this->get_score() . ' / ' . $this->get_complex_content_object_question()->get_weight();
            }
            else
            {
                $html[] = '&nbsp;';
            }
        }
        else
        {
            for ($i = - $this->get_complex_content_object_question()->get_weight(); $i <=
                 $this->get_complex_content_object_question()->get_weight(); $i ++)
            {
                $score[$i] = $i;
            }
            
            $renderer = $this->form->defaultRenderer();
            
            $this->form->addElement('select', $this->complex_content_object_question->get_id() . '_score', '', $score);
            $renderer->setElementTemplate('{element}', $this->complex_content_object_question->get_id() . '_score');
            $defaults[$this->complex_content_object_question->get_id() . '_score'] = $this->get_score();
            $this->form->setDefaults($defaults);
        }
        
        $html[] = '</div>';
        
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="answer">';
        
        $description = $this->question->get_description();
        
        if ($this->question->has_description())
        {
            $html[] = '<div class="description">';
            
            $renderer = new ContentObjectResourceRenderer($this, $description);
            $html[] = $renderer->run();
            
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }
        
        $html[] = '<div class="clear"></div>';
        
        $this->form->addElement('html', implode(PHP_EOL, $html));
    }

    public function display_feedback()
    {
        if (! $this->can_change)
        {
            $html[] = '<div class="splitter">';
            $html[] = Translation::get('CourseAdministratorFeedback');
            $html[] = '</div>';
            $html[] = '<div class="with_borders">';
            if (! $this->feedback)
            {
                $html[] = '<div class="warning-message">' . Translation::get('NotYetRatedWarning') . '</div>';
            }
            $this->form->addElement('html', implode(PHP_EOL, $html));
            $html = array();
            if ($this->feedback)
            {
                $html[] = $this->feedback;
            }
            $html[] = '</div>';
            $this->form->addElement('html', implode(PHP_EOL, $html));
        }
        else
        {
            $html[] = '<div class="splitter">';
            $html[] = Translation::get('CourseAdministratorFeedback');
            $html[] = '</div>';
            $html[] = '<div class="with_borders">';
            
            if (! $this->feedback)
            {
                $html[] = '<div class="warning-message">' . Translation::get('NotYetRatedWarning') . '</div>';
            }
            
            $this->form->addElement('html', implode(PHP_EOL, $html));
            $html = array();
            
            $this->form->add_html_editor($this->complex_content_object_question->get_id() . '_feedback', '', false);
            $defaults[$this->complex_content_object_question->get_id() . '_feedback'] = $this->get_feedback();
            $this->form->setDefaults($defaults);
            
            $html[] = '</div>';
            $this->form->addElement('html', implode(PHP_EOL, $html));
        }
    }

    public function render_footer()
    {
        $html[] = '</div>';
        $html[] = '</div>';
        
        $footer = implode(PHP_EOL, $html);
        return $footer;
    }

    public function add_borders()
    {
        return false;
    }
}
