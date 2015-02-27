<?php
namespace Chamilo\Core\Repository\ContentObject\OrderingQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\Wizard\Inc\AssessmentQuestionResultDisplay;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package
 *          core\repository\content_object\ordering_question\integration\core\repository\content_object\assessment\display
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ResultDisplay extends AssessmentQuestionResultDisplay
{

    public function get_question_result()
    {
        $configuration = $this->get_assessment_result_processor()->get_assessment_viewer()->get_configuration();
        
        $html = array();
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th style="text-align: center;" class="list">' . Translation :: get('YourOrder') . '</th>';
        
        if ($configuration->show_solution())
        {
            $html[] = '<th style="text-align: center;" class="list">' . Translation :: get('CorrectOrder') . '</th>';
        }
        
        $html[] = '<th>' . Translation :: get('Answer') . '</th>';
        
        if ($configuration->show_answer_feedback())
        {
            $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        }
        
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $answers = $this->get_question()->get_options();
        $user_answers = $this->get_answers();
        
        foreach ($answers as $i => $answer)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            
            $correct_answer = $user_answers[$i + 1] == $answer->get_order();
            
            if ($configuration->show_correction() || $configuration->show_solution())
            {
                if ($correct_answer)
                {
                    $result = ' <img style="vertical-align: middle;" src="' . Theme :: getInstance()->getImagePath() .
                         'answer_correct.png" alt="' . Translation :: get('Correct') . '" title="' .
                         Translation :: get('Correct') . '" style="" />';
                }
                else
                {
                    $result = ' <img style="vertical-align: middle;" src="' . Theme :: getInstance()->getImagePath() .
                         'answer_wrong.png" alt="' . Translation :: get('Wrong') . '" title="' .
                         Translation :: get('Wrong') . '" />';
                }
            }
            else
            {
                $result = '';
            }
            
            if ($user_answers[$i + 1] == - 1)
            {
                $html[] = '<td style="text-align: center;">' . Translation :: get('NoAnswer') . $result . '</td>';
            }
            else
            {
                $html[] = '<td style="text-align: center;">' . $user_answers[$i + 1] . $result . '</td>';
            }
            
            if ($configuration->show_solution())
            {
                $html[] = '<td style="text-align: center;">' . $answer->get_order() . '</td>';
            }
            
            $object_renderer = new ContentObjectResourceRenderer(
                $this->get_assessment_result_processor()->get_assessment_viewer(), 
                $answer->get_value());
            
            $html[] = '<td>' . $object_renderer->run() . '</td>';
            
            if (AnswerFeedbackDisplay :: allowed(
                $configuration, 
                $this->get_complex_content_object_question(), 
                true, 
                $correct_answer))
            {
                $object_renderer = new ContentObjectResourceRenderer(
                    $this->get_assessment_result_processor()->get_assessment_viewer(), 
                    $answer->get_feedback());
                $html[] = '<td>' . $object_renderer->run() . '</td>';
            }
            
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode(PHP_EOL, $html);
    }
}
