<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestion;
use Chamilo\Libraries\Translation\Translation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $html = [];
        
        $lo = $this->get_content_object();
        $options = $lo->get_options();
        $type = $lo->get_answer_type();
        switch ($type)
        {
            case AssessmentMultipleChoiceQuestion::ANSWER_TYPE_RADIO :
                $type = 'radio';
                break;
            case AssessmentMultipleChoiceQuestion::ANSWER_TYPE_CHECKBOX :
                $type = 'checkbox';
                break;
        }
        
        $html[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th></th>';
        $html[] = '<th>' . Translation::get('Options') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        foreach ($options as $index => $option)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td><input type="' . $type . '" name="option[]"/></td>';
            
            $renderer = new ContentObjectResourceRenderer( $option->get_value());
            $html[] = '<td>' . $renderer->run() . '</td>';
            
            $html[] = '</tr>';
        }
        
        $html[] = '</tbody>';
        $html[] = '</table>';
        return implode(PHP_EOL, $html);
    }
}
