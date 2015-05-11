<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

class HtmlFullRenditionImplementation extends HtmlRenditionImplementation
{

    function render()
    {
        $html = array();

        $html[] = ContentObjectRendition :: launch($this);
        $html[] = '<h4>' . Translation :: get('QuestionPreview') . '</h4>';
        $html[] = '<div style="border: 1px solid whitesmoke; padding: 10px; margin-bottom: 10px;">';
        $html[] = $this->get_question_preview();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    function get_question_preview($nr = null, $complex_question_id = null)
    {
        $content_object = $this->get_content_object();
        $min = $content_object->get_low();
        $max = $content_object->get_high();

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
        $table_header[] = '<th class="info" >' . Translation :: get('ChooseYourRating') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $table_header[] = '<tr>';
        $table_header[] = '<td>';
        $html[] = implode(PHP_EOL, $table_header);

        $html[] = '<select class="rating_slider" name="' . $complex_question_id . '" >';
        $html[] = '</option>';
        for ($i = $min; $i <= $max; $i ++)
        {
            $scores[$i] = $i;
            $html[] = '<option value="' . $i . '" > ' . $i . '';
            $html[] = '</option>';
        }
        $html[] = '</select>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath(
                'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating',
                true) . 'Form.js');

        $table_footer[] = '</td>';
        $table_footer[] = '</tr>';
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $html[] = implode(PHP_EOL, $table_footer);

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';

        return implode(PHP_EOL, $html);
    }
}