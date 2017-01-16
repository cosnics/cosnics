<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentQuestionResultDisplay;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestion;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package
 *          core\repository\content_object\assessment_select_question\integration\core\repository\content_object\assessment\display
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ResultDisplay extends AssessmentQuestionResultDisplay
{

    public function get_question_result()
    {
        $complex_content_object_question = $this->get_complex_content_object_question();
        $question = $this->get_question();
        $configuration = $this->getViewerApplication()->get_configuration();
        
        $html = array();
        $html[] = '<table class="table table-striped table-bordered table-hover table-data take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox_answer"></th>';
        $html[] = '<th>' . Translation::get('Answer') . '</th>';
        
        if ($configuration->show_answer_feedback())
        {
            $html[] = '<th>' . Translation::get('Feedback') . '</th>';
        }
        
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        $type = $this->get_question()->get_answer_type();
        $answers = $this->get_answers();
        
        if ($type == AssessmentSelectQuestion::ANSWER_TYPE_RADIO)
        {
            foreach ($this->get_question()->get_options() as $i => $option)
            {
                $options[$i] = $option;
            }
        }
        else
        {
            $options = $this->get_question()->get_options();
        }
        
        foreach ($options as $i => $option)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            
            if ($type == AssessmentSelectQuestion::ANSWER_TYPE_RADIO)
            {
                $is_given_answer = $answers[0] == $i;
                
                if ($is_given_answer)
                {
                    $selected = ' checked ';
                    
                    if ($configuration->show_correction() || $configuration->show_solution())
                    {
                        if ($option->is_correct())
                        {
                            $result = '<img src="' . Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerCorrect') .
                                 '" alt="' . Translation::get('Correct') . '" title="' . Translation::get('Correct') .
                                 '" style="" />';
                        }
                        else
                        {
                            $result = '<img src="' . Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerWrong') .
                                 '" alt="' . Translation::get('Wrong') . '" title="' . Translation::get('Wrong') . '" />';
                        }
                    }
                    else
                    {
                        $result = '';
                    }
                }
                else
                {
                    $selected = '';
                    
                    if ($configuration->show_solution())
                    {
                        if ($option->is_correct())
                        {
                            $result = '<img src="' . Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerCorrect') .
                                 '" alt="' . Translation::get('Correct') . '" title="' . Translation::get('Correct') .
                                 '" />';
                        }
                        else
                        {
                            $result = '';
                        }
                    }
                    else
                    {
                        $result = '';
                    }
                }
                
                $html[] = '<td><input type="radio" name="yourchoice_' .
                     $this->get_complex_content_object_question()->get_id() . '" value="' . $i . '" disabled' . $selected .
                     '/>' . $result . '</td>';
            }
            else
            {
                $is_given_answer = in_array($i, $answers[0]);
                $is_correct = $option->is_correct();
                
                if ($is_given_answer)
                {
                    $selected = ' checked ';
                }
                else
                {
                    $selected = '';
                }
                
                if (($is_given_answer && $configuration->show_correction()) || $configuration->show_solution())
                {
                    if ($is_correct)
                    {
                        $result = '<img src="' . Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerCorrect') .
                             '" alt="' . Translation::get('Correct') . '" title="' . Translation::get('Correct') .
                             '" style="" />';
                    }
                    else
                    {
                        $result = '<img src="' . Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerWrong') .
                             '" alt="' . Translation::get('Wrong') . '" title="' . Translation::get('Wrong') . '" />';
                    }
                }
                else
                {
                    $result = '';
                }
                
                $html[] = '<td><input type="checkbox" name="yourchoice' . $i . '" disabled' . $selected . '/>' . $result .
                     '</td>';
            }
            
            $html[] = '<td>' . $option->get_value() . '</td>';
            
            if (AnswerFeedbackDisplay::allowed(
                $configuration, 
                $this->get_complex_content_object_question(), 
                $is_given_answer, 
                $option->is_correct()))
            {
                $object_renderer = new ContentObjectResourceRenderer(
                    $this->getViewerApplication(), 
                    $option->get_feedback());
                
                $html[] = '<td>' . $object_renderer->run() . '</td>';
            }
            elseif ($configuration->show_answer_feedback())
            {
                $html[] = '<td></td>';
            }
            
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode(PHP_EOL, $html);
    }

    public function needsDescriptionBorder()
    {
        return true;
    }
}
