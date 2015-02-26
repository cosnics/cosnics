<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Implementation\Rendition\Html\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Implementation\Rendition\Html\HtmlFormRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\ComplexMultipleChoice;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.content_object.survey_multiple_choice_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class HtmlFormSelectRenditionImplementation extends HtmlFormRenditionImplementation
{

    private $question;

    function render(FormValidator $formvalidator, ComplexMultipleChoice $complex_content_object_item, 
        $answer = null)
    {
        // $formvalidator = $this->get_context()->get_formvalidator();
        $renderer = $formvalidator->get_renderer();
        // $complex_question = $this->get_context()->get_complex_content_object_item();
        $this->question = $this->get_content_object();
        
        $options = $this->question->get_options();
        
        $type = $this->question->get_answer_type();
        $question_id = $this->question->get_id();
        
        foreach ($options as $option)
        {
            $answer_options[$option->get_id()] = $option->get_value();
        }
        
        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);
        
        $question_name = $complex_content_object_item->get_id();
        
        if ($type == 'checkbox')
        {
            $advanced_select = $formvalidator->createElement(
                'advmultiselect', 
                $question_name, 
                '', 
                $answer_options, 
                array('style' => 'width: 200px;', 'class' => 'advanced_select_question'));
            $advanced_select->setButtonAttributes('add', 'class="add"');
            $advanced_select->setButtonAttributes('remove', 'class="remove"');
            $formvalidator->addElement($advanced_select);
            if ($answer)
            {
                $formvalidator->setDefaults(array($question_name => array_values($answer[0])));
            }
        }
        else
        {
            $select_box = $formvalidator->createElement(
                'select', 
                $question_name, 
                '', 
                $answer_options, 
                'class="select_question"');
            $formvalidator->addElement($select_box);
            
            if ($answer)
            {
                $formvalidator->setDefaults(array($question_name => $answer[$question_name]));
            }
        }
        
        $renderer->setElementTemplate($element_template, $question_name);
    }

    function add_borders()
    {
        return true;
    }

    function get_instruction()
    {
        $instruction = array();
        
        $type = $this->question->get_answer_type();
        
        if ($type == 'radio' && $this->question->has_instruction())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('SelectYourChoice');
            $instruction[] = '</div>';
        }
        elseif ($type == 'checkbox' && $this->question->has_instruction())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('SelectYourChoices');
            $instruction[] = '</div>';
        }
        else
        {
            $instruction = array();
        }
        
        return implode("\n", $instruction);
    }
}
?>