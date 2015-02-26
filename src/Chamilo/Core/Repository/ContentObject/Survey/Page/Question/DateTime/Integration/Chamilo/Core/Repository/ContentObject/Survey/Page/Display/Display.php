<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Integration\Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;

class Display extends QuestionDisplay
{

    private $question;

    function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $complex_question = $complex_content_object_path_node->get_complex_content_object_item();
        $this->question = $complex_content_object_path_node->get_content_object();
        $formvalidator = $this->get_formvalidator();
        
        $html = array();
        $html[] = '<table class="data_table take_survey">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        
        if ($this->question->get_date() == 1)
        {
            $html[] = '<th class="info" >' . Translation :: get('EnterDate') . '</th>';
            $html[] = '</tr>';
            $html[] = '</thead>';
            
            $html[] = '<tbody>';
            $html[] = '<tr>';
            $html[] = '<td>';
            
            $html[] = '<div class="datepicker" id=" . $complex_question->get_id() . "></div>';
            $html[] = '<script type="text/javascript" src="' .
                 Path :: getInstance()->namespaceToFullPath(__NAMESPACE__, true) . 'resources/javascript/date.js' .
                 '"></script>';
            $html[] = '</td>';
            $html[] = '</tr>';
            $html[] = '</tbody>';
        }
        
        if ($this->question->get_time() == 1)
        {
            $html[] = '<th class="info" >' . Translation :: get('EnterTime') . '</th>';
            $html[] = '</tr>';
            $html[] = '</thead>';
            $html[] = '<tbody>';
            
            $html[] = '<tr>';
            $html[] = '<td>';
            $html[] = '<div class="timepicker" id=" . $complex_question->get_id() . "></div>';
            $html[] = '<script type="text/javascript" src="' .
                 Path :: getInstance()->namespaceToFullPath(__NAMESPACE__, true) . 'resources/javascript/time.js' .
                 '"></script>';
            $html[] = '</td>';
            $html[] = '</tr>';
            $html[] = '</tbody>';
        }
        
        $html[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $html));
    }

    function get_instruction()
    {
    }
}
?>