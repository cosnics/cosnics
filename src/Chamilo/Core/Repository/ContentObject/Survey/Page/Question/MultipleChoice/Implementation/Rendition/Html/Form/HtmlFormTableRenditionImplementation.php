<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Implementation\Rendition\Html\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Implementation\Rendition\Html\HtmlFormRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\ComplexMultipleChoice;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\MultipleChoice;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_multiple_choice_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormTableRenditionImplementation extends HtmlFormRenditionImplementation
{

    function render(FormValidator $formvalidator, ComplexMultipleChoice $complex_content_object_item, 
        $answer = null)
    {
        // $formvalidator = $this->get_context()->get_formvalidator();
        $renderer = $formvalidator->defaultRenderer();
        
        $options = $this->get_content_object()->get_options();
        $type = $this->get_content_object()->get_answer_type();
        
        $table_header = array();
        $table_header[] = '<table class="data_table take_survey">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox"></th>';
        $table_header[] = '<th class="info" >' . $this->get_instruction() . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));
        
        $question_id = $complex_content_object_item->get_id();
        
        foreach ($options as $option)
        {
            $i = $option->get_id();
            $group = array();
            
            if ($type == MultipleChoice :: ANSWER_TYPE_RADIO)
            {
                $option_name = $question_id;
                
                $radio_button = $formvalidator->createElement('radio', $option_name, null, null, $i);
                
                if ($answer)
                {
                    $key = $answer[$option_name];
                    if ($i == $key)
                    {
                        $formvalidator->setDefaults(array($option_name => $key));
                    }
                }
                
                $group[] = $radio_button;
                $group[] = $formvalidator->createElement('static', null, null, $option->get_value());
            }
            elseif ($type == MultipleChoice :: ANSWER_TYPE_CHECKBOX)
            {
                $option_name = $question_id . '_' . $i;
                
                $check_box = $formvalidator->createElement('checkbox', $option_name, null, null, null, $i);
                
                if ($answer[$option_name] == $i)
                {
                    $formvalidator->setDefaults(array($option_name => $i));
                }
                
                $group[] = $check_box;
                $group[] = $formvalidator->createElement('static', null, null, $option->get_value());
            }
            
            $formvalidator->addGroup($group, 'option_' . $i, null, '', false);
            
            $renderer->setElementTemplate(
                '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 
                'option_' . $i);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $i);
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_footer));
    }

    function get_instruction()
    {
        $type = $this->get_content_object()->get_answer_type();
        
        if ($type == MultipleChoice :: ANSWER_TYPE_RADIO && $this->get_content_object()->has_instruction())
        {
            $title = Translation :: get('SelectYourChoice');
        }
        elseif ($type == MultipleChoice :: ANSWER_TYPE_CHECKBOX &&
             $this->get_content_object()->has_instruction())
        {
            $title = Translation :: get('SelectYourChoices');
        }
        else
        {
            $title = '';
        }
        
        return $title;
    }
}
?>