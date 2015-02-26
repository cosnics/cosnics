<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Gender\Integration\Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
use Chamilo\Libraries\Platform\Translation;

class Display extends QuestionDisplay
{

    function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $complex_question = $complex_content_object_path_node->get_complex_content_object_item();
        $question = $complex_content_object_path_node->get_content_object();
        $formvalidator = $this->get_formvalidator();
        $complex_question_id = $complex_question->get_id();
        
        $html = array();
        $html[] = '<table class="data_table take_survey">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox" ></th>';
        $html[] = '<th class="info" >' . Translation :: get('SelectYourChoice') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        
        $html[] = '<tr class="row_even">';
        $html[] = '<td><input type="radio" value="0" name="' . $complex_question_id . '"/></td>';
        $html[] = '<td>' . Translation :: get('Male') . '</td>';
        $html[] = '</tr>';
        
        $html[] = '<tr class="row_odd">';
        $html[] = '<td><input type="radio" value="1" name="' . $complex_question_id . '"/></td>';
        $html[] = '<td>' . Translation :: get('Female') . '</td>';
        $html[] = '</tr>';
        $html[] = '</tbody>';
        $html[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $html));
    }

    function get_instruction()
    {
    }
}
?>