<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Integration\Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;

class Display extends QuestionDisplay
{

    function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $complex_question = $complex_content_object_path_node->get_complex_content_object_item();
        $question = $complex_content_object_path_node->get_content_object();
        $formvalidator = $this->get_formvalidator();
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="info" >' . $this->get_instruction() . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $table_header[] = '<tr>';
        $table_header[] = '<td>';
        $formvalidator->addElement('html', implode("\n", $table_header));
        
        $this->add_html_editor($question, $formvalidator);
        
        $table_footer[] = '</td>';
        $table_footer[] = '</tr>';
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
    }

    function add_html_editor($question, $formvalidator)
    {
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['toolbar'] = 'Assessment';
        
        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);
        $renderer = $this->get_renderer();
        
        $name = $question->get_id();
        $formvalidator->add_html_editor($name, '', false, $html_editor_options);
        
        // $answer = $this->get_answer();
        
        // if ($answer)
        // {
        // $formvalidator->setDefaults(array($name => $answer[$name]));
        // }
        
        $renderer->setElementTemplate($element_template, $name);
    }

    function get_instruction()
    {
        // $question = $this->get_question();
        
        // if ($question->has_instruction())
        // {
        // $instruction = Translation :: get('EnterAnswer');
        // }
        // else
        // {
        // $instruction = '';
        // }
        
        // return $instruction;
    }
}
?>