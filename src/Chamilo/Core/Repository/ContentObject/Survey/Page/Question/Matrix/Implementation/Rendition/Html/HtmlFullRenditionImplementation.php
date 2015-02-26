<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass\Matrix;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFullRenditionImplementation extends HtmlRenditionImplementation
{

    function render()
    {
        $html[] = ContentObjectRendition :: launch($this);
        $html[] = '<h4>' . Translation :: get('QuestionPreview') . '</h4>';
        $html[] = '<div style="border: 1px solid whitesmoke; padding: 10px; margin-bottom: 10px;">';
        $html[] = $this->get_question_preview();
        $html[] = '</div>';
        return implode("\n", $html);
    }

    function get_question_preview($nr = null, $complex_question_id = null)
    {
        $content_object = $this->get_content_object();
        $matches = $content_object->get_matches();
        $options = $content_object->get_options();
        $type = $content_object->get_matrix_type();
        
        $html = array();
        
        $html[] = '<div  class="question" >';
        $html[] = '<div class="title">';
        $html[] = '<div class="number">';
        $html[] = '<div class="bevel">';
        $html[] = $nr != null ? $nr : 'nr.';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="text">';
        $html[] = '<div class="bevel">';
        $title = $content_object->get_question();
        $html[] = $title;
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        $html[] = '<div class="instruction">';
        if ($content_object->has_instruction())
        {
            $html[] = $content_object->get_instruction();
        }
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        
        $html[] = '<div class="answer">';
        $html[] = '<div class="clear"></div>';
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="caption"></th>';
        $match_objects = array();
        
        while ($match = $matches->next_result())
        {
            $match_objects[] = $match;
            $table_header[] = '<th class="center">' . strip_tags($match->get_value()) . '</th>';
        }
        
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $html[] = implode("\n", $table_header);
        $index = 0;
        while ($option = $options->next_result())
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $option->get_value() . '</td>';
            
            foreach ($match_objects as $match)
            {
                if ($type == Matrix :: MATRIX_TYPE_RADIO)
                {
                    $answer_name = $complex_question_id . '_' . $option->get_id();
                    $html[] = '<td style="text-align: center;"><input type="radio" value="' . $match->get_id() .
                         '" name="' . $answer_name . '"/></td>';
                }
                elseif ($type == Matrix :: MATRIX_TYPE_CHECKBOX)
                {
                    $answer_name = $complex_question_id . '_' . $option->get_id() . '_' . $match->get_id();
                    $html[] = '<td style="text-align: center;"><input type="checkbox" value="' . $match->get_id() .
                         '" name="' . $answer_name . '"/></td>';
                }
            }
            $index ++;
            $html[] = '</tr>';
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        
        $html[] = implode("\n", $table_footer);
        
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode("\n", $html);
    }
}