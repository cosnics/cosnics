<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Storage\DataClass\Open;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Platform\Translation;

/**
 * A form to create/update a survey_open_question
 */
class OpenForm extends ContentObjectForm
{

    function build_basic_form()
    {
        $this->addElement('category', Translation :: get('Question'));
        $this->add_textfield(
            Open :: PROPERTY_QUESTION, 
            Translation :: get('Question'), 
            true, 
            array('size' => '100', 'id' => 'title', 'style' => 'width: 95%'));
        $this->add_html_editor(Open :: PROPERTY_INSTRUCTION, Translation :: get('Instruction'), false);
        
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
        
        $object = new Open();
        $object->set_question($values[Open :: PROPERTY_QUESTION]);
        $object->set_instruction($values[Open :: PROPERTY_INSTRUCTION]);
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $values = $this->exportValues();
        
        $object = $this->get_content_object();
        $object->set_question($values[Open :: PROPERTY_QUESTION]);
        $object->set_instruction($values[Open :: PROPERTY_INSTRUCTION]);
        return parent :: update_content_object();
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        $defaults[Open :: PROPERTY_QUESTION] = $defaults[Open :: PROPERTY_QUESTION] == null ? $object->get_question() : $defaults[Open :: PROPERTY_QUESTION];
        $defaults[Open :: PROPERTY_INSTRUCTION] = $object->get_instruction();
        parent :: setDefaults($defaults);
    }
}

?>
