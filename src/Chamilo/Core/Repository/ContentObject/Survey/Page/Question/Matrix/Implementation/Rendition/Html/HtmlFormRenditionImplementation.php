<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Implementation\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass\Matrix;

/**
 *
 * @package repository.content_object.survey_matrix_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormRenditionImplementation extends \Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Implementation\Rendition\Html\HtmlFormRenditionImplementation
{

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function initialize()
    {
        $formValidator = parent :: initialize();
        $renderer = $formValidator->get_renderer();
        
        $question = $this->get_content_object();
        $question_id = $this->getQuestionId();
   
        $options = $question->get_options();
        $matches = $question->get_matches();
        $match_objects = array();
        $type = $question->get_matrix_type();
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="caption" style="width: 30%;"></th>';
        
        foreach ($matches as $match)
        {
            $match_objects[] = $match;
            $table_header[] = '<th class="center">' . trim(strip_tags($match->get_value())) . '</th>';
        }
        
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_header));
        
        foreach ($options as $option)
        {
            $group = array();
            $i = $option->get_id();
            $group[] = $formValidator->createElement(
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
                    
                    $radio = $formValidator->createElement('radio', $option_name, null, null, $j);
                    
                    $group[] = $radio;
                }
                elseif ($type == Matrix :: MATRIX_TYPE_CHECKBOX)
                {
                    $option_name = $question_id . '_' . $i . '_' . $j;
                    
                    $checkbox = $formValidator->createElement('checkbox', $option_name, null, null, null, $j);
                    $group[] = $checkbox;
                }
            }
            
            $formValidator->addGroup($group, 'matrix_option_' . $i, null, '', false);
            
            $renderer->setElementTemplate(
                '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 
                'matrix_option_' . $i);
            $renderer->setGroupElementTemplate('<td style="text-align: center;">{element}</td>', 'matrix_option_' . $i);
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formValidator->addElement('html', implode(PHP_EOL, $table_footer));
        return $formValidator;
    }
  
}