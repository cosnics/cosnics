<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentQuestionResultDisplay;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass\AssessmentMatchingQuestion;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package
 *          core\repository\content_object\assessment_matching_question\integration\core\repository\content_object\assessment\display
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ResultDisplay extends AssessmentQuestionResultDisplay
{

    public function get_question_result()
    {
        $labels = array(
            'A', 
            'B', 
            'C', 
            'D', 
            'E', 
            'F', 
            'G', 
            'H', 
            'I', 
            'J', 
            'K', 
            'L', 
            'M', 
            'N', 
            'O', 
            'P', 
            'Q', 
            'R', 
            'S', 
            'T', 
            'U', 
            'V', 
            'W', 
            'X', 
            'Y', 
            'Z');
        
        $configuration = $this->getViewerApplication()->get_configuration();
        
        $html = array();

        $html[] = '<table class="table table-bordered table-hover table-data take_assessment"' .
            ' style="border-top: 2px solid #dddddd; border-bottom: 1px solid #dddddd;">';

        $html[] = '<thead style="background-color: #f5f5f5; border-bottom: 2px solid #dddddd;">';
        $html[] = '<tr>';
        $html[] = '<th class="list"></th>';
        $html[] = '<th>' . Translation::get('Question') . '</th>';
        $html[] = '<th>' . Translation::get('YourAnswer') . '</th>';
        
        if ($configuration->show_solution())
        {
            $html[] = '<th>' . Translation::get('Correct') . '</th>';
        }
        
        if ($configuration->show_answer_feedback())
        {
            $html[] = '<th>' . Translation::get('Feedback') . '</th>';
        }
        
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $answers = $this->get_answers();
        
        $options = $this->get_question()->get_options();
        foreach ($options as $i => $option)
        {
            $valid_answer = $answers[$i] == $option->get_match();
            
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            
            $label = $i + 1;
            $html[] = '<td>' . $label . '. </td>';
            
            $object_renderer = new ContentObjectResourceRenderer($this->getViewerApplication(), $option->get_value());
            $html[] = '<td>' . $object_renderer->run() . '</td>';
            
            if ($configuration->show_correction() || $configuration->show_solution())
            {
                if ($valid_answer)
                {
                    $result = ' <img src="' . Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerCorrect') .
                         '" alt="' . Translation::get('Correct') . '" title="' . Translation::get('Correct') .
                         '" style="" />';
                }
                else
                {
                    $result = ' <img src="' . Theme::getInstance()->getImagePath(__NAMESPACE__, 'AnswerWrong') .
                         '" alt="' . Translation::get('Wrong') . '" title="' . Translation::get('Wrong') . '" />';
                }
            }
            else
            {
                $result = '';
            }
            
            if ($answers[$i] == - 1)
            {
                $html[] = '<td>' . Translation::get('NoAnswer') . $result . '</td>';
            }
            else
            {
                $html[] = '<td>' . $labels[$answers[$i]] . $result . '</td>';
            }
            
            if ($configuration->show_solution())
            {
                $html[] = '<td>' . $labels[$option->get_match()] . '</td>';
            }
            
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
            
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';

        $html[] = '<table class="table table-bordered table-hover table-data take_assessment"' .
            ' style="margin-top: 30px; border-top: 2px solid #dddddd;">';
        $html[] = '<thead style="background-color: #f5f5f5; border-bottom: 2px solid #dddddd;">';
        $html[] = '<tr>';
        $html[] = '<th class="list"></th>';
        $html[] = '<th>' . Translation::get('PossibleAnswers') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $label = 'A';
        $matches = $this->get_question()->get_matches();
        foreach ($matches as $i => $match)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $label . '.</td>';

            if ($this->get_question()->get_display() == AssessmentMatchingQuestion::DISPLAY_LIST)
            {

                $object_renderer = new ContentObjectResourceRenderer($this->getViewerApplication(), $match);
                $html[] = '<td>' . $object_renderer->run() . '</td>';
            }
            else
            {
                $html[] = '<td>' . strip_tags($match) . '</td>';
            }
            $html[] = '</tr>';
            $label ++;
        }

        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode(PHP_EOL, $html);
    }
}
