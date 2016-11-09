<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice\Storage\DataClass\Choice;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * A form to create/update a survey_open_question
 */
class ChoiceForm extends ContentObjectForm
{
    const TAB_GENERAL = 'general';
    const TAB_QUESTION = 'question';

    private static $html_editor_options = array(
        FormValidatorHtmlEditorOptions :: OPTION_HEIGHT => '75', 
        FormValidatorHtmlEditorOptions :: OPTION_COLLAPSE_TOOLBAR => true);

    /**
     * Prepare all the different tabs
     */
    function prepareTabs()
    {
        $this->addElement(
            'html', 
            ResourceManager :: getInstance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice', 
                    true) . 'Form.js'));
        
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self :: TAB_QUESTION, 
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString(self :: TAB_QUESTION)->upperCamelize()), 
                Theme :: getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Choice', 
                    'Tab/' . self :: TAB_QUESTION), 
                'build_question_form'));
        
        $this->addDefaultTab();
        $this->addMetadataTabs();
    }

    function build_question_form()
    {
        $this->add_textfield(
            Choice :: PROPERTY_QUESTION, 
            Translation :: get('Question'), 
            true, 
            array('size' => '100', 'id' => 'question', 'style' => 'width: 95%'));
        $this->add_html_editor(
            Choice :: PROPERTY_INSTRUCTION, 
            Translation :: get('Instruction'), 
            false, 
            self :: $html_editor_options);
        
        $this->addElement('category', Translation :: get('Types'));
        
        $choices[] = $this->createElement(
            'radio', 
            Choice :: PROPERTY_QUESTION_TYPE, 
            '', 
            Translation :: get('Yes/No'), 
            Choice :: TYPE_YES_NO, 
            array('onclick' => 'javascript:timewindow_hide(\'repeat_timewindow\')', 'id' => Choice :: PROPERTY_QUESTION_TYPE));
        $choices[] = $this->createElement(
            'radio', 
            Choice :: PROPERTY_QUESTION_TYPE, 
            '', 
            Translation :: get('Other'), 
            Choice :: TYPE_OTHER, 
            array('onclick' => 'javascript:timewindow_show(\'repeat_timewindow\')'));
        $this->addGroup($choices, null, Translation :: get('QuestionType'), '', false);
        
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
        
        $this->addGroup($yes_no_elements, Choice :: PROPERTY_QUESTION_TYPE, '', '', false);
        
        $this->addElement('html', '</div>');
        $this->addElement(
            'html', 
            "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var expiration = document.getElementById('" . Choice :: PROPERTY_QUESTION_TYPE . "');
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
    }
    
    // Inherited
    function create_content_object()
    {
        $values = $this->exportValues();
        
        $object = new Choice();
        $object->set_question($values[Choice :: PROPERTY_QUESTION]);
        $object->set_instruction($values[Choice :: PROPERTY_INSTRUCTION]);
        if ($values[Choice :: PROPERTY_QUESTION_TYPE] == Choice :: TYPE_OTHER)
        {
            $object->set_question_type(Choice :: TYPE_OTHER);
            
            $object->set_first_choice($values[Choice :: PROPERTY_FIRST_CHOICE]);
            $object->set_second_choice($values[Choice :: PROPERTY_SECOND_CHOICE]);
        }
        else
        {
            $object->set_question_type(Choice :: TYPE_YES_NO);
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
        $defaults[Choice :: PROPERTY_QUESTION] = $defaults[Choice :: PROPERTY_QUESTION] == null ? $object->get_question() : $defaults[Choice :: PROPERTY_QUESTION];
        $defaults[Choice :: PROPERTY_INSTRUCTION] = $object->get_instruction();
        
        $defaults[Choice :: PROPERTY_QUESTION_TYPE] = $object->get_question_type();
        if ($object->get_question_type() == Choice :: TYPE_OTHER)
        {
            $defaults[Choice :: PROPERTY_FIRST_CHOICE] = $object->get_first_choice();
            $defaults[Choice :: PROPERTY_SECOND_CHOICE] = $object->get_second_choice();
            $defaults[Choice :: PROPERTY_QUESTION_TYPE] = Choice :: TYPE_OTHER;
        }else{
            $defaults[Choice :: PROPERTY_QUESTION_TYPE] = Choice :: TYPE_YES_NO;
        }
        
        parent :: setDefaults($defaults);
    }
}

?>
