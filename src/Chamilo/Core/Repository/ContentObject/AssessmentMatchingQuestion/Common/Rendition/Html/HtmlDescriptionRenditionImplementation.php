<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Translation\Translation;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $content_object = $this->get_content_object();
        $matches = $content_object->get_matches();
        $options = $content_object->get_options();
        
        $html = array();
        
        // Adding the matches
        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="cell-stat-x3"></th>';
        $table_header[] = '<th>' . Translation::get('PossibleMatches') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $html[] = implode(PHP_EOL, $table_header);
        
        $match_label = 'A';
        foreach ($matches as $index => $match)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $match_label . '</td>';
            
            $renderer = new ContentObjectResourceRenderer($this->get_context(), $match);
            $html[] = '<td>' . $renderer->run() . '</td>';
            
            $html[] = '</tr>';
            $match_label ++;
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $html[] = implode(PHP_EOL, $table_footer);
        
        $html[] = '<br />';
        
        // Adding the items to be matched
        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="cell-stat-x3"></th>';
        $table_header[] = '<th>' . Translation::get('MatchOptionAnswer') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $html[] = implode(PHP_EOL, $table_header);
        
        $answer_count = 0;
        foreach ($options as $index => $option)
        {
            $answer_number = ($answer_count + 1) . '.';
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td>' . $answer_number . '</td>';
            
            $renderer = new ContentObjectResourceRenderer($this->get_context(), $option->get_value());
            $html[] = '<td>' . $renderer->run() . '</td>';
            
            $html[] = '</tr>';
            $answer_count ++;
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $html[] = implode(PHP_EOL, $table_footer);
        
        return implode(PHP_EOL, $html);
    }
}
