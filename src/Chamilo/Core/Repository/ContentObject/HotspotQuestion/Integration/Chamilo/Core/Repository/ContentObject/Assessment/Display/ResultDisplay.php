<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentQuestionResultDisplay;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\File\ImageManipulation\ImageManipulation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
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

        $scaledDimensions = ImageManipulation::rescale($dimensions[0], $dimensions[1], 600, 450);

        $html[] = '<div style="border: 1px solid #B5CAE7; border-top: none; padding: 10px;">';
        $html[] = '<div id="hotspot_container_' . $question_id . '" class="hotspot_container"><div id="hotspot_image_' .
            $question_id . '" class="hotspot_image" style="width: ' .
            $scaledDimensions[ImageManipulation::DIMENSION_WIDTH] . 'px; height: ' .
            $scaledDimensions[ImageManipulation::DIMENSION_HEIGHT] . 'px; background-size: ' .
            $scaledDimensions[ImageManipulation::DIMENSION_WIDTH] . 'px ' .
            $scaledDimensions[ImageManipulation::DIMENSION_HEIGHT] . 'px;background-image: url(' .
            Manager::get_document_downloader_url(
                $image_object->get_id(), $image_object->calculate_security_code()
            ) . ')"></div></div>';
        $html[] = '<script src="' . htmlspecialchars(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion', true) .
                'Plugin/jquery.draw.js'
            ) . '"></script>';
        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion', true) .
            'HotspotQuestionResultDisplay.js'
        );
        $html[] = '<div class="clearfix"></div></div>';

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
            '#8000ff'
        );

        $html[] = '<table class="table table-striped table-bordered table-hover table-data take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="cell-stat-x3"></th>';

        if ($configuration->show_correction() || $configuration->show_solution())
        {
            $html[] = '<th class="cell-stat-x3"></th>';
        }

        $html[] = '<th>' . Translation::get('Answer') . '</th>';

        if ($configuration->show_answer_feedback())
        {
            $html[] = '<th>' . Translation::get('Feedback') . '</th>';
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
                if ($valid_answer)
                {
                    $glyph = new FontAwesomeGlyph(
                        'check', array('text-success'),
                        Translation::get('Correct', array(), 'Chamilo\Core\Repository\ContentObject\Assessment'), 'fas'
                    );
                }
                else
                {
                    $glyph = new FontAwesomeGlyph(
                        'times', array('text-danger'),
                        Translation::get('Wrong', array(), 'Chamilo\Core\Repository\ContentObject\Assessment'), 'fas'
                    );
                }

                $html[] = '<td>' . $glyph->render() . '</td>';
            }

            $object_renderer = new ContentObjectResourceRenderer($this->getViewerApplication(), $answer->get_answer());
            $html[] = '<td>' . $object_renderer->run() . '</td>';

            if (AnswerFeedbackDisplay::allowed(
                $configuration, $this->get_complex_content_object_question(), true, $valid_answer
            ))
            {
                $object_renderer = new ContentObjectResourceRenderer(
                    $this->getViewerApplication(), $answer->get_comment()
                );

                $html[] = '<td>' . $object_renderer->run() . '</td>';
            }

            $html[] =
                '<input type="hidden" name="coordinates_' . $this->get_complex_content_object_question()->get_id() .
                '_' . $i . '" value="' . $answer->get_hotspot_coordinates() . '" />';

            $html[] = '<input type="hidden" name="hotspot_user_answers_' .
                $this->get_complex_content_object_question()->get_id() . '_' . $i . '" value="' . $user_answers[$i] .
                '" />';

            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode(PHP_EOL, $html);
    }

    public function is_valid_answer($answer, $user_answer)
    {
        $hotspot_coordinates = unserialize($answer->get_hotspot_coordinates());
        $hotspot_coordinates[] = $hotspot_coordinates[0];

        $polygon = new PointInPolygon($hotspot_coordinates);
        $is_inside = $polygon->is_inside(unserialize($user_answer));

        switch ($is_inside)
        {
            case PointInPolygon::POINT_INSIDE :
                return true;
            case PointInPolygon::POINT_BOUNDARY :
                return true;
            case PointInPolygon::POINT_VERTEX :
                return true;
        }

        return false;
    }
}
