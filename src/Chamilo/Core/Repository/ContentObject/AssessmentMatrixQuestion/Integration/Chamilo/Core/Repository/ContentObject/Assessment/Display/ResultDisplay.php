<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentQuestionResultDisplay;
use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass\AssessmentMatrixQuestion;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package
 *          core\repository\content_object\assessment_matrix_question\integration\core\repository\content_object\assessment\display
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ResultDisplay extends AssessmentQuestionResultDisplay
{

    public function get_question_result()
    {
        $answers = $this->get_answers();
        $options = $this->get_question()->get_options();
        $matches = $this->get_question()->get_matches();
        $type = $this->get_question()->get_matrix_type();
        $configuration = $this->getViewerApplication()->get_configuration();
        
        $html = array();
        $html[] = '<table class="table table-striped table-bordered table-hover table-data take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th></th>';
        
        foreach ($matches as $match)
        {
            $html[] = '<th style="text-transform: none; font-size: small;">' . $match . '</th>';
        }
        
        if ($configuration->show_answer_feedback())
        {
            $html[] = '<th>' . Translation::get('Feedback') . '</th>';
        }
        
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        foreach ($options as $i => $option)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            
            $object_renderer = new ContentObjectResourceRenderer($this->getViewerApplication(), $option->get_value());
            $html[] = '<td>' . $object_renderer->run() . '</td>';
            
            foreach ($matches as $j => $match)
            {
                $html[] = '<td>';
                if ($type == AssessmentMatrixQuestion::MATRIX_TYPE_RADIO)
                {
                    if ($answers[$i] == $j && ! is_null($answers[$i]))
                    {
                        $selected = " checked ";
                        
                        if ($configuration->show_correction() || $configuration->show_solution())
                        {
                            if ($option->get_matches() == $j)
                            {
                                $result = '<img src="' .
                                     Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerCorrect') . '" alt="' .
                                     Translation::get('Correct') . '" title="' . Translation::get('Correct') . '" />';
                            }
                            else
                            {
                                $result = '<img src="' .
                                     Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerWrong') . '" alt="' .
                                     Translation::get('Wrong') . '" title="' . Translation::get('Wrong') . '" />';
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
                            if ($option->get_matches() == $j)
                            {
                                $result = '<img src="' . Theme :: getInstance()->getImagePath(__NAMESPACE__, 'AnswerCorrect') .
                                     '" alt="' . Translation :: get('Correct') . '" title="' .
                                     Translation :: get('Correct') . '" />';
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
                    
                    $html[] = '<input type="radio" name="yourchoice_' .
                         $this->get_complex_content_object_question()->get_id() . '_' . $i . '" value="' . $j .
                         '" disabled' . $selected . '/>';
                    $html[] = $result;
                }
                else
                {
                    if (array_key_exists($j, $answers[$i]))
                    {
                        $selected = " checked ";
                        
                        if ($configuration->show_correction() || $configuration->show_solution())
                        {
                            if (in_array($j, $option->get_matches()))
                            {
                                $result = '<img src="' .
                                     Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerCorrect') . '" alt="' .
                                     Translation::get('Correct') . '" title="' . Translation::get('Correct') .
                                     '" style="" />';
                            }
                            else
                            {
                                $result = '<img src="' .
                                     Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerWrong') . '" alt="' .
                                     Translation::get('Wrong') . '" title="' . Translation::get('Wrong') . '" />';
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
                            if (in_array($j, $option->get_matches()))
                            {
                                $result = '<img src="' .
                                     Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerCorrect') . '" alt="' .
                                     Translation::get('Correct') . '" title="' . Translation::get('Correct') .
                                     '" style="" />';
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
                    
                    $html[] = '<input type="checkbox" name="yourchoice_' . $i . '_' . $j . '" disabled' . $selected .
                         '/>';
                    $html[] = $result;
                }
                
                $html[] = '</td>';
            }
            
            if ($configuration->show_answer_feedback())
            {
                $valid_answer = ($type == AssessmentMatrixQuestion::MATRIX_TYPE_RADIO &&
                     $answers[$i] == $option->get_matches()) || ($type ==
                     AssessmentMatrixQuestion::MATRIX_TYPE_CHECKBOX &&
                     count(array_diff(array_keys($answers[$i]), $option->get_matches())) == 0);
                
                if (AnswerFeedbackDisplay::allowed(
                    $configuration, 
                    $this->get_complex_content_object_question(), 
                    true, 
                    $valid_answer))
                {
                    $object_renderer = new ContentObjectResourceRenderer(
                        $this->getViewerApplication(), 
                        $option->get_feedback());
                    $html[] = '<td>' . $object_renderer->run() . '</td>';
                }
            }
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode(PHP_EOL, $html);
    }
}
