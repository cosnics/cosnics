<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open\Storage\DataClass\Open;
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
class OpenForm extends ContentObjectForm
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
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open',
                    true) . 'Form.js'));
    
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self :: TAB_QUESTION,
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString(self :: TAB_QUESTION)->upperCamelize()),
                Theme :: getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Open',
                    'Tab/' . self :: TAB_QUESTION),
                'build_question_form'));
    
        $this->addDefaultTab();
        $this->addMetadataTabs();
    }
    
    function build_question_form()
    {
        $this->add_textfield(
            Open :: PROPERTY_QUESTION, 
            Translation :: get('Question'), 
            true, 
            array('size' => '100', 'id' => 'question', 'style' => 'width: 95%'));
        $this->add_html_editor(Open :: PROPERTY_INSTRUCTION, Translation :: get('Instruction'), false, self :: $html_editor_options);
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
