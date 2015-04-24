<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Storage\DataClass\DateTime;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Platform\Translation;

/**
 * A form to create/update a survey_open_question
 */
class DateTimeForm extends ContentObjectForm
{

    function build_basic_form()
    {
        $this->addElement('category', Translation :: get('Question'));
        $this->add_textfield(
            DateTime :: PROPERTY_QUESTION, 
            Translation :: get('Question'), 
            true, 
            array('size' => '100', 'id' => 'title', 'style' => 'width: 95%'));
        $this->add_html_editor(DateTime :: PROPERTY_INSTRUCTION, Translation :: get('Instruction'), false);
        
        $this->addElement('category', Translation :: get('Types'));
        $this->addElement('checkbox', DateTime :: PROPERTY_DATE, Translation :: get('DateQuestion'));
        $this->addElement('checkbox', DateTime :: PROPERTY_TIME, Translation :: get('TimeQuestion'));
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
        
        $object = new DateTime();
        $object->set_question($values[DateTime :: PROPERTY_QUESTION]);
        $object->set_instruction($values[DateTime :: PROPERTY_INSTRUCTION]);
        $object->set_date($values[DateTime :: PROPERTY_DATE]);
        $object->set_time($values[DateTime :: PROPERTY_TIME]);
        
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $values = $this->exportValues();
        
        $object = $this->get_content_object();
        $object->set_question($values[DateTime :: PROPERTY_QUESTION]);
        $object->set_instruction($values[DateTime :: PROPERTY_INSTRUCTION]);
        
        $object->set_date($values[DateTime :: PROPERTY_DATE]);
        $object->set_time($values[DateTime :: PROPERTY_TIME]);
        
        return parent :: update_content_object();
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        $defaults[DateTime :: PROPERTY_QUESTION] = $defaults[DateTime :: PROPERTY_QUESTION] ==
             null ? $object->get_question() : $defaults[DateTime :: PROPERTY_QUESTION];
        $defaults[DateTime :: PROPERTY_INSTRUCTION] = $object->get_instruction();
        $defaults[DateTime :: PROPERTY_DATE] = $object->get_date();
        $defaults[DateTime :: PROPERTY_TIME] = $object->get_time();
        
        parent :: setDefaults($defaults);
    }
}

?>
