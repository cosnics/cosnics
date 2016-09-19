<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentQuestionResultDisplay;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package
 *          core\repository\content_object\assessment_rating_question\integration\core\repository\content_object\assessment\display
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
        $correct_answer = $answers[0] == $this->get_question()->get_correct();
        $configuration = $this->getViewerApplication()->get_configuration();
        
        $html = array();
        $html[] = '<table class="table table-striped table-bordered table-hover table-data take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list">' . Translation :: get('YourRating') . '</th>';
        
        if ($configuration->show_solution())
        {
            $html[] = '<th class="list">' . Translation :: get('CorrectRating') . '</th>';
        }
        
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $html[] = '<tr>';
        
        if ($configuration->show_correction() || $configuration->show_solution())
        {
            if ($correct_answer)
            {
                $result = ' <img style="vertical-align: middle;" src="' .
                     Theme :: getInstance()->getImagePath(__NAMESPACE__, 'AnswerCorrect') . '" alt="' .
                     Translation :: get('Correct') . '" title="' . Translation :: get('Correct') . '" style="" />';
            }
            else
            {
                $result = ' <img style="vertical-align: middle;" src="' .
                     Theme :: getInstance()->getImagePath(__NAMESPACE__, 'AnswerWrong') . '" alt="' .
                     Translation :: get('Wrong') . '" title="' . Translation :: get('Wrong') . '" />';
            }
        }
        else
        {
            $result = '';
        }
        
        $html[] = '<td>' . $answers[0] . $result . '</td>';
        
        if ($configuration->show_solution())
        {
            $html[] = '<td>' . $this->get_question()->get_correct() . '</td>';
        }
        
        $html[] = '</tr>';
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        if (AnswerFeedbackDisplay :: allowed(
            $configuration, 
            $this->get_complex_content_object_question(), 
            true, 
            $correct_answer))
        {
            $object_renderer = new ContentObjectResourceRenderer(
                $this->getViewerApplication(), 
                $this->get_question()->get_feedback());
            
            $html[] = '<div class="splitter">';
            $html[] = Translation :: get('Feedback');
            $html[] = '</div>';
            
            $html[] = '<div style="padding: 10px; border-left: 1px solid #B5CAE7; border-right: 1px solid #B5CAE7; border-bottom: 1px solid #B5CAE7;">';
            $html[] = $object_renderer->run();
            $html[] = '</div>';
        }
        
        return implode(PHP_EOL, $html);
    }
}
