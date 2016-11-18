<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Platform\Translation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $html = array();
        
        $lo = $this->get_content_object();
        $options = $lo->get_options();
        $type = $lo->get_answer_type();
        
        $html[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation::get($type == 'radio' ? 'SelectCorrectAnswer' : 'SelectCorrectAnswers') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $select_options = array();
        foreach ($options as $option)
        {
            $select_options[] = '<option>' . $option->get_value() . '</option>';
        }
        
        $html[] = '<tr>';
        $html[] = '<td>';
        $html[] = '<select style="width: 200px;"' . ($type == 'checkbox' ? ' multiple="true"' : '') . '>';
        $html[] = implode(PHP_EOL, $select_options);
        $html[] = '</select>';
        $html[] = '</td>';
        $html[] = '</tr>';
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        return implode(PHP_EOL, $html);
    }
}
