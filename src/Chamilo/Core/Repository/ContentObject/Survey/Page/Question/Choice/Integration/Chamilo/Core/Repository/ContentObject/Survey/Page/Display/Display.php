<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Form\ChoiceForm;
use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
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
        
        if ($this->question->get_question_type() == ChoiceForm :: TYPE_YES_NO)
        {
            $html[] = '<th class="checkbox" ></th>';
            $html[] = '<th class="info" >' . Translation :: get('SelectYourChoice') . '</th>';
            $html[] = '</tr>';
            $html[] = '</thead>';
            $html[] = '<tbody>';
            
            $html[] = '<tr class="row_even">';
            
            $html[] = '<td><input type="radio" value="0" name="' . $complex_question->get_id() . '"/></td>';
            $html[] = '<td>' . Translation :: get('AnswerYes') . '</td>';
            $html[] = '</tr>';
            
            $html[] = '<tr class="row_odd">';
            $html[] = '<td><input type="radio" value="1" name="' . $complex_question->get_id() . '"/></td>';
            $html[] = '<td>' . Translation :: get('AnswerNo') . '</td>';
            $html[] = '</tr>';
            
            $html[] = '</tbody>';
        }
        
        if ($this->question->get_question_type() == ChoiceForm :: TYPE_OTHER)
        {
            $html[] = '<th class="checkbox" ></th>';
            $html[] = '<th class="info" >' . Translation :: get('SelectYourChoice') . '</th>';
            $html[] = '</tr>';
            $html[] = '</thead>';
            $html[] = '<tbody>';
            
            $html[] = '<tr class="row_even">';
            
            $html[] = '<td><input type="radio" value="0"  name="' . $complex_question->get_id() . '"/></td>';
            $html[] = '<td>' . $this->question->get_first_choice() . '</td>';
            $html[] = '</tr>';
            
            $html[] = '<tr class="row_odd">';
            $html[] = '<td><input type="radio" value="1"  name="' . $complex_question->get_id() . '"/></td>';
            $html[] = '<td>' . $this->question->get_second_choice() . '</td>';
            $html[] = '</tr>';
            
            $html[] = '</tbody>';
        }
        
        $html[] = '</table>';
        $formvalidator->addElement('html', implode(PHP_EOL, $html));
    }

    function get_instruction()
    {
    }
}
?>