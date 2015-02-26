<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Integration\Chamilo\Core\Repository\ContentObject\Survey\Page\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass\Matrix;
use Chamilo\Core\Repository\ContentObject\Survey\Page\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Display\QuestionDisplay;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

class Display extends QuestionDisplay
{

    function process(ComplexContentObjectPathNode $complex_content_object_path_node, $answer)
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $complex_question = $complex_content_object_path_node->get_complex_content_object_item();
        $question = $complex_content_object_path_node->get_content_object();
        
        $options = $question->get_options();
        $matches = $question->get_matches();
        $match_objects = array();
        $type = $question->get_matrix_type();
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="caption" style="width: 30%;"></th>';
        
        while ($match = $matches->next_result())
        {
            $match_objects[] = $match;
            $table_header[] = '<th class="center">' . trim(strip_tags($match->get_value())) . '</th>';
        }
        
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode("\n", $table_header));
        
        $question_id = $complex_question->get_id();
        
        while ($option = $options->next_result())
        {
            $group = array();
            $i = $option->get_id();
            $group[] = $formvalidator->createElement(
                'static', 
                null, 
                null, 
                '<div style="text-align: left;">' . $option->get_value() . '</div>');
            
            foreach ($match_objects as $match)
            {
                $j = $match->get_id();
                if ($type == Matrix :: MATRIX_TYPE_RADIO)
                {
                    $option_name = $question_id . '_' . $i;
                    
                    $radio = $formvalidator->createElement('radio', $option_name, null, null, $j);
                    
                    if ($answer)
                    {
                        if ($answer[$option_name] == $j)
                        {
                            $formvalidator->setDefaults(array($option_name => $j));
                        }
                    }
                    
                    $group[] = $radio;
                }
                elseif ($type == Matrix :: MATRIX_TYPE_CHECKBOX)
                {
                    $option_name = $question_id . '_' . $i . '_' . $j;
                    
                    $checkbox = $formvalidator->createElement('checkbox', $option_name, null, null, null, $j);
                    if ($answer)
                    {
                        if ($answer[$option_name] == $j)
                        {
                            $formvalidator->setDefaults(array($option_name => $j));
                        }
                    }
                    $group[] = $checkbox;
                }
            }
            
            $formvalidator->addGroup($group, 'matrix_option_' . $i, null, '', false);
            
            $renderer->setElementTemplate(
                '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 
                'matrix_option_' . $i);
            $renderer->setGroupElementTemplate('<td style="text-align: center;">{element}</td>', 'matrix_option_' . $i);
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
        $formvalidator->addElement(
            'html', 
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getBasePath(true) .
                     'repository/content_object/survey_matrix_question/integration/repository/content_object/survey_page/resources/javascript/matrix_question_display.js'));
    }

    function add_border()
    {
        return false;
    }
    /*
     * (non-PHPdoc) @see \repository\content_object\survey_page\QuestionDisplay::get_instruction()
     */
    public function get_instruction()
    {
        // TODO Auto-generated method stub
    }
}
?>