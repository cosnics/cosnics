<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Platform\Translation;

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
        $options = $content_object->get_options();
        $type = $content_object->get_answer_type();
        
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
        
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="info" >' . Translation :: get($type == 'radio' ? 'SelectYourChoice' : 'SelectYourChoices') .
             '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $select_options = array();
        
        while ($option = $options->next_result())
        {
            $select_options[] = '<option  value="' . $option->get_id() . '"  >' . $option->get_value() . '</option>';
        }
        
        $html[] = '<tr>';
        $html[] = '<td>';
        $html[] = '<select style="width: 200px;"' . ($type == 'checkbox' ? ' multiple="true"' : '') . '  name="' .
             $complex_question_id . '"  >';
        $html[] = implode("\n", $select_options);
        $html[] = '</select>';
        $html[] = '</td>';
        $html[] = '</tr>';
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        
        return implode("\n", $html);
    }
}
?>