<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\PointInPolygon;

/**
 * $Id: hotspot_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.assessment.component.result_viewer.question_result_display
 */
class HotspotQuestionResultDisplay extends QuestionResultDisplay
{

    public function display_question_result()
    {
        $question = $this->get_question();
        $question_id = $this->get_complex_content_object_question()->get_id();
        $answers = $question->get_answers();
        
        $image_object = $question->get_image_object();
        $dimensions = getimagesize($image_object->get_full_path());
        
        $html[] = '<div style="border: 1px solid #B5CAE7; border-top: none; padding: 10px;">';
        
        $html[] = '<div id="hotspot_container_' . $question_id . '" class="hotspot_container"><div id="hotspot_image_' .
             $question_id . '" class="hotspot_image" style="width: ' . $dimensions[0] . 'px; height: ' . $dimensions[1] .
             'px; background-image: url(' . $image_object->get_url() . ')"></div></div>';
        $html[] = '<script type="text/javascript" src="' . htmlspecialchars(
            Path :: getInstance()->getPluginPath('Chamilo\Configuration', true) . 'jquery/jquery.draw.js') . '"></script>';
        $html[] = '<script type="text/javascript" src="' . htmlspecialchars(
            Path :: getInstance()->getPluginPath('Chamilo\Configuration', true) . 'jquery/phpjs.js') . '"></script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getBasePath(true) .
                 'repository/content_object/hotspot_question/resources/javascript/hotspot_question_result_display.js');
        
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
        $html[] = '<th class="list"></th>';
        $html[] = '<th>' . Translation :: get('Answer') . '</th>';
        if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
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
            $html[] = '<td>' . ($valid_answer ? Theme :: getInstance()->getImage('answer_correct') : Theme :: getInstance()->getImage(
                'answer_wrong')) . '</td>';
            
            $object_renderer = new ContentObjectResourceRenderer($this->get_results_viewer(), $answer->get_answer());
            $html[] = '<td>' . $object_renderer->run() . '</td>';
            
            if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
            {
                if (($this->get_complex_content_object_question()->get_feedback_answer() && ! $valid_answer) ||
                     ! $this->get_complex_content_object_question()->get_feedback_answer())
                {
                    $object_renderer = new ContentObjectResourceRenderer(
                        $this->get_results_viewer(), 
                        $answer->get_comment());
                    
                    $html[] = '<td>' . $object_renderer->run() . '</td>';
                }
                else
                {
                    $html[] = '<td></td>';
                }
            }
            $html[] = '<input type="hidden" name="coordinates_' . $this->get_complex_content_object_question()->get_id() .
                 '_' . $i . '" value="' . $answer->get_hotspot_coordinates() . '" />';
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        return implode("\n", $html);
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
