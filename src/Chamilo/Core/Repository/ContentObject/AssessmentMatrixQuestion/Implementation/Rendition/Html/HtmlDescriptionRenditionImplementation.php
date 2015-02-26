<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass\AssessmentMatrixQuestion;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    public function get_description()
    {
        $content_object = $this->get_content_object();
        $question_id = $content_object->get_id();
        $matches = $content_object->get_matches();
        $options = $content_object->get_options();
        $type = $content_object->get_matrix_type();
        
        $html = array();
        
        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="caption"></th>';
        
        foreach ($matches as $match)
        {
            $table_header[] = '<th class="center" style="text-transform: none; font-size: small;">' . strip_tags($match) .
                 '</th>';
        }
        
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $html[] = implode("\n", $table_header);
        
        foreach ($options as $index => $option)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            
            $renderer = new ContentObjectResourceRenderer($this->get_context(), $option->get_value());
            $html[] = '<td>' . $renderer->run() . '</td>';
            
            foreach ($matches as $j => $match)
            {
                if ($type == AssessmentMatrixQuestion :: MATRIX_TYPE_RADIO)
                
                {
                    $answer_name = $question_id . '_' . $index . '_0';
                    $html[] = '<td style="text-align: center;"><input type="radio" name="' . $answer_name . '"/></td>';
                }
                elseif ($type == AssessmentMatrixQuestion :: MATRIX_TYPE_CHECKBOX)
                
                {
                    $answer_name = $question_id . '_' . $index . '[' . $j . ']';
                    $html[] = '<td style="text-align: center;"><input type="checkbox" name="' . $answer_name . '"/></td>';
                }
            }
            
            $html[] = '</tr>';
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $html[] = implode("\n", $table_footer);
        
        return implode("\n", $html);
    }
}
