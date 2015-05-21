<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Integration\Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Storage\DataClass\DateTime;

class Display extends QuestionDisplay
{

    private $question;

    function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $complex_question = $complex_content_object_path_node->get_complex_content_object_item();
        $question = $complex_content_object_path_node->get_content_object();
        
        
        $tableHeader = array();
        $tableHeader[] = '<table class="data_table take_survey">';
        $tableHeader[] = '<thead>';
        $tableHeader[] = '<tr>';
        
        if ($question->get_question_type() == DateTime :: TYPE_DATE)
        {
            $tableHeader[] = '<th class="info" >' . Translation :: get('EnterDate') . '</th>';
        }
        elseif ($question->get_question_type() == DateTime::TYPE_TIME)
        {
            $html[] = '<th class="info" >' . Translation :: get('EnterTime') . '</th>';
        }
        
        $tableHeader[] = '</tr>';
        $tableHeader[] = '</thead>';
        $tableHeader[] = '<tbody>';
        $tableHeader[] = '<tr>';
        $tableHeader[] = '<td>';
        $formvalidator->addElement('html', implode(PHP_EOL, $tableHeader));
        
        $namespace = $question->package();
        
        if ($question->get_question_type() == DateTime::TYPE_DATE)
        {
            
            $formvalidator->add_datepicker($complex_question->get_id(), 'label?', false);
//             $html[] = '<div id="datepicker_'.$complex_question->get_id().'" name="'. $complex_question->get_id().'"></div>';
            
//             $html[] = '<script type="text/javascript" src="' .
//                  Path :: getInstance()->getJavascriptPath(
//                     $namespace, 
//                     true) . 'Date.js' . '"></script>';
        }
        
        if ($question->get_question_type() == DateTime::TYPE_TIME)
        {
            $html = array();
            $html[] = '<div id="timepicker_'.$complex_question->get_id().'" name="'. $complex_question->get_id().'"></div>';
            $html[] = '<script type="text/javascript" src="' .
                 Path :: getInstance()->getJavascriptPath(
                    $namespace, 
                    true) . 'Time.js' . '"></script>';
            $formvalidator->addElement('html', implode(PHP_EOL, $html));
        }
        
        $tableFooter = array();
        $tableFooter[] = '</td>';
        $tableFooter[] = '</tr>';
        $tableFooter[] = '</tbody>';
        $tableFooter[] = '</table>';
        $formvalidator->addElement('html', implode(PHP_EOL, $tableFooter));
    }

    function get_instruction()
    {
    }
}
?>