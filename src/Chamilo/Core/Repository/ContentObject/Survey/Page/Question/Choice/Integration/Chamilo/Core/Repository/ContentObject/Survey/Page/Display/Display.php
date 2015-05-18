<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Integration\Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
use Chamilo\Libraries\Platform\Translation;

class Display extends QuestionDisplay
{

    private $question;

    function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $complex_question = $complex_content_object_path_node->get_complex_content_object_item();
        $question = $complex_content_object_path_node->get_content_object();
        
        $options = $question->getOptions();
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox" ></th>';
        $table_header[] = '<th class="info" >' . Translation :: get('SelectYourChoice') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));
        
        $question_id = $complex_question->get_id();
        
        foreach ($options as $i => $option)
        {
            $group = array();
            
            $option_name = $question_id . '_1' ;
            
            $radio = $formvalidator->createElement('radio', $option_name, null, null, $i);
            
            $group[] = $radio;
            
            if ($answer)
            {
                if ($answer[$option_name] == $i)
                {
                    $formvalidator->setDefaults(array($option_name => $i));
                }
            }
            
            $group[] = $formvalidator->createElement(
                'static', 
                null, 
                null, 
                '<div style="text-align: left;">' . $option . '</div>');
            
            $formvalidator->addGroup($group, 'choice_option_' . $i, null, '', false);
            
            $renderer->setElementTemplate(
                '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 
                'choice_option_' . $i);
            $renderer->setGroupElementTemplate('<td style="text-align: center;">{element}</td>', 'choice_option_' . $i);
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_footer));
    }

    function get_instruction()
    {
    }
}
?>