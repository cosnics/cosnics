<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_matching_question
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
        
        // Adding the matches
        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th class="info" >' . Translation :: get('PossibleMatches') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $html[] = implode("\n", $table_header);
        
        $match_label = 'A';
        
        $index = 0;
        $match_labels = array();
        while ($match = $matches->next_result())
        {
            
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $match_label . '</td>';
            $html[] = '<td>' . $match->get_value() . '</td>';
            $html[] = '</tr>';
            $match_labels[$match->get_id()] = $match_label;
            $match_label ++;
            $index ++;
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $html[] = implode("\n", $table_footer);
        
        $html[] = '<br />';
        
        // Adding the items to be matched
        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th class="info" colspan="2">' . Translation :: get('ChooseYourOptionMatch') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $html[] = implode("\n", $table_header);
        
        $answer_count = 0;
        $index = 0;
        while ($option = $options->next_result())
        {
            $answer_number = ($answer_count + 1) . '.';
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $answer_number . '</td>';
            $html[] = '<td>' . $option->get_value() . '</td>';
            $html[] = '<td>';
            $html[] = '<select name="' . $complex_question_id . '_' . $option->get_id() . '" >';
            
            foreach ($match_labels as $match_id => $label)
            {
                $html[] = '<option value="' . $match_id . '">' . $label . '</option>';
            }
            
            $html[] = '</select>';
            $html[] = '</td>';
            $html[] = '</tr>';
            $answer_count ++;
            $index ++;
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
?>