<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\Wizard\Inc\AssessmentQuestionResultDisplay;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\PointInPolygon;

/**
 *
 * @package
 *          core\repository\content_object\hotspot_question\integration\core\repository\content_object\assessment\display
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ResultDisplay extends AssessmentQuestionResultDisplay
{

    public function get_question_result()
    {
        $question = $this->get_question();
        $question_id = $this->get_complex_content_object_question()->get_id();
        $answers = $question->get_answers();
        $configuration = $this->getViewerApplication()->get_configuration();
        $html = array();

        $image_object = $question->get_image_object();
        $dimensions = getimagesize($image_object->get_full_path());

        $html[] = '<div style="border: 1px solid #B5CAE7; border-top: none; padding: 10px;">';
        $html[] = '<div id="hotspot_container_' . $question_id . '" class="hotspot_container"><div id="hotspot_image_' .
             $question_id . '" class="hotspot_image" style="width: ' . $dimensions[0] . 'px; height: ' . $dimensions[1] .
             'px; background-image: url(' . $image_object->get_url() . ')"></div></div>';
        $html[] = '<script type="text/javascript" src="' . htmlspecialchars(
            Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion', true) .
                 'Plugin/jquery.draw.js') . '"></script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion', true) .
                 'HotspotQuestionResultDisplay.js');
        $html[] = '<div class="clear"></div></div>';

        $user_answers = $this->get_answers();
        $colors = array(
            '#ff0000',
            '#f2ef00',
            '#00ff00',
            '#00ffff',
            '#0000ff',
            '#ff00ff',
            '#0080ff',
            '#ff0080',
            '#00ff80',
            '#ff8000',
            '#8000ff');

        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list"></th>';

        if ($configuration->show_correction() || $configuration->show_solution())
        {
            $html[] = '<th class="list"></th>';
        }

        $html[] = '<th>' . Translation :: get('Answer') . '</th>';

        if ($configuration->show_answer_feedback())
        {
            $html[] = '<th>' . Translation :: get('Feedback') . '</th>';
        }

        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        foreach ($answers as $i => $answer)
        {
            $valid_answer = $this->is_valid_answer($answer, $user_answers[$i]);

            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td><div class="colour_box" style="background-color: ' . $colors[$i] . ';"></div></td>';

            if ($configuration->show_correction() || $configuration->show_solution())
            {
                $html[] = '<td>' . ($valid_answer ? Theme :: getInstance()->getImage('answer_correct') : Theme :: getInstance()->getImage(
                    'answer_wrong')) . '</td>';
            }

            $object_renderer = new ContentObjectResourceRenderer($this->getViewerApplication(), $answer->get_answer());
            $html[] = '<td>' . $object_renderer->run() . '</td>';

            if (AnswerFeedbackDisplay :: allowed(
                $configuration,
                $this->get_complex_content_object_question(),
                true,
                $valid_answer))
            {
                $object_renderer = new ContentObjectResourceRenderer(
                    $this->getViewerApplication(),
                    $answer->get_comment());

                $html[] = '<td>' . $object_renderer->run() . '</td>';
            }

            $html[] = '<input type="hidden" name="coordinates_' . $this->get_complex_content_object_question()->get_id() .
                 '_' . $i . '" value="' . $answer->get_hotspot_coordinates() . '" />';
            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode(PHP_EOL, $html);
    }

    public function is_valid_answer($answer, $user_answer)
    {
        $hotspot_coordinates = $answer->get_hotspot_coordinates();

        $polygon = new PointInPolygon(unserialize($hotspot_coordinates));
        $is_inside = $polygon->is_inside(unserialize($user_answer));

        switch ($is_inside)
        {
            case PointInPolygon :: POINT_INSIDE :
                return true;
            case PointInPolygon :: POINT_BOUNDARY :
                return true;
            case PointInPolygon :: POINT_VERTEX :
                return true;
        }

        return false;
    }
}
