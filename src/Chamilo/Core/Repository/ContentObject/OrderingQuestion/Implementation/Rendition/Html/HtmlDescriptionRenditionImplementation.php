<?php
namespace Chamilo\Core\Repository\ContentObject\OrderingQuestion\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\OrderingQuestion\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Platform\Translation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    public function get_description()
    {
        $html = array();
        
        $lo = $this->get_content_object();
        $options = $lo->get_options();
        
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox"></th>';
        $html[] = '<th>' . Translation :: get('PutAnswersCorrectOrder') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        
        $order_options = $this->get_order_options();
        
        foreach ($options as $index => $option)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>';
            $html[] = '<select>';
            $html[] = $order_options;
            $html[] = '</select>';
            $html[] = '</td>';
            
            $renderer = new ContentObjectResourceRenderer($this->get_context(), $option->get_value());
            $html[] = '<td>' . $renderer->run() . '</td>';
            
            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';
        return implode("\n", $html);
    }

    public function get_order_options()
    {
        $answer_count = count($this->get_content_object()->get_options());
        
        $options = array();
        for ($i = 1; $i <= $answer_count; $i ++)
        {
            $options[] = '<option>' . $i . '</option>';
        }
        
        return implode("\n", $options);
    }
}
