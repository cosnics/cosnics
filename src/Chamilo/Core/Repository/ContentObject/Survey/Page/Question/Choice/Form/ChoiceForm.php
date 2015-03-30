<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Storage\DataClass\Choice;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Platform\Translation;

/**
 * A form to create/update a survey_open_question
 */
class ChoiceForm extends ContentObjectForm
{
    const TYPE_YES_NO = 0;
    const TYPE_OTHER = 1;
    const PARAM_TYPE = 'type';

    function build_basic_form()
    {
        $this->addElement('category', Translation :: get('Question'));
        $this->add_textfield(
            Choice :: PROPERTY_QUESTION, 
            Translation :: get('Question'), 
            true, 
            array('size' => '100', 'id' => 'title', 'style' => 'width: 95%'));
        $this->add_html_editor(Choice :: PROPERTY_INSTRUCTION, Translation :: get('Instruction'), false);
        
        $this->addElement('category', Translation :: get('Types'));
        
        $choices[] = $this->createElement(
            'radio', 
            self :: PARAM_TYPE, 
            '', 
            Translation :: get('Yes/No'), 
            self :: TYPE_YES_NO, 
            array('onclick' => 'javascript:timewindow_hide(\'repeat_timewindow\')', 'id' => self :: PARAM_TYPE));
        $choices[] = $this->createElement(
            'radio', 
            self :: PARAM_TYPE, 
            '', 
            Translation :: get('Other'), 
            self :: TYPE_OTHER, 
            array('onclick' => 'javascript:timewindow_show(\'repeat_timewindow\')'));
        $this->addGroup($choices, null, Translation :: get('QuestionType'), '<br />', false);
        
        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="repeat_timewindow">');
        $yes_no_elements = array();
        $yes_no_elements[] = $this->createElement('static', '', null, Translation :: get('FirstChoice'));
        $yes_no_elements[] = $this->createElement(
            'text', 
            Choice :: PROPERTY_FIRST_CHOICE, 
            '', 
            array('style' => 'width:300px', 'maxlength' => 50));
        $yes_no_elements[] = $this->createElement('static', '', null, Translation :: get('SecondChoice'));
        $yes_no_elements[] = $this->createElement(
            'text', 
            Choice :: PROPERTY_SECOND_CHOICE, 
            '', 
            array('style' => 'width:300px', 'maxlength' => 50));
        
        $this->addGroup($yes_no_elements, self :: PARAM_TYPE, '', '<br/>', false);
        
        $this->addElement('html', '</div>');
        $this->addElement(
            'html', 
            "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('" . self :: PARAM_TYPE . "');
					if (expiration.checked)
					{
						timewindow_hide('repeat_timewindow');
					}
					function timewindow_show(item) {
						el = document.getElementById(item);
						el.style.display='';
					}
					function timewindow_hide(item) {
						el = document.getElementById(item);
						el.style.display='none';
					}
					/* ]]> */
					</script>\n");
        
        $this->addElement('category');
        $this->addElement('category');
    }

    protected function build_creation_form()
    {
        $this->build_basic_form();
        parent :: build_creation_form();
    }

    protected function build_editing_form()
    {
        $this->build_basic_form();
        parent :: build_editing_form();
    }
    
    // Inherited
    function create_content_object()
    {
        $values = $this->exportValues();
        
        $object = new Choice();
        $object->set_question($values[Choice :: PROPERTY_QUESTION]);
        $object->set_instruction($values[Choice :: PROPERTY_INSTRUCTION]);
        if (self :: PARAM_TYPE == self :: TYPE_OTHER)
        {
            $object->set_question_type(self :: TYPE_OTHER);
            
            $object->set_first_choice($values[Choice :: PROPERTY_FIRST_CHOICE]);
            $object->set_second_choice($values[Choice :: PROPERTY_SECOND_CHOICE]);
        }
        else
        {
            $object->set_question_type(self :: TYPE_YES_NO);
        }
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $values = $this->exportValues();
        
        $object = $this->get_content_object();
        $object->set_question($values[Choice :: PROPERTY_QUESTION]);
        $object->set_instruction($values[Choice :: PROPERTY_INSTRUCTION]);
        
        $object->set_question_type($values[Choice :: PROPERTY_QUESTION_TYPE]);
        
        $object->set_first_choice($values[Choice :: PROPERTY_FIRST_CHOICE]);
        $object->set_second_choice($values[Choice :: PROPERTY_SECOND_CHOICE]);
        
        return parent :: update_content_object();
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        $defaults[Choice :: PROPERTY_QUESTION] = $defaults[Choice :: PROPERTY_QUESTION] ==
             null ? $object->get_question() : $defaults[Choice :: PROPERTY_QUESTION];
        $defaults[Choice :: PROPERTY_INSTRUCTION] = $object->get_instruction();
        
        $defaults[Choice :: PROPERTY_QUESTION_TYPE] = $object->get_question_type();
        if ($object->get_question_type() == self :: TYPE_OTHER)
        {
            $defaults[Choice :: PROPERTY_FIRST_CHOICE] = $object->get_first_choice();
            $defaults[Choice :: PROPERTY_SECOND_CHOICE] = $object->get_second_choice();
        }
        
        parent :: setDefaults($defaults);
    }
}

?>
