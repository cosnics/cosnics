<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Gender\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Gender\Storage\DataClass\Gender;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 * A form to create/update a survey_open_question
 */
class GenderForm extends ContentObjectForm
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
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Gender',
                    true) . 'Form.js'));
    
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self :: TAB_QUESTION,
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString(self :: TAB_QUESTION)->upperCamelize()),
                Theme :: getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Gender',
                    'Tab/' . self :: TAB_QUESTION),
                'build_question_form'));
    
        $this->addDefaultTab();
        $this->addMetadataTabs();
    }
    
    function build_question_form()
    {
        $this->add_textfield(
            Gender :: PROPERTY_QUESTION, 
            Translation :: get('Question'), 
            true, 
            array('size' => '100', 'id' => 'question', 'style' => 'width: 95%'));
        $this->add_html_editor(Gender :: PROPERTY_INSTRUCTION, Translation :: get('Instruction'), false, self :: $html_editor_options);
        
    }
   
    // Inherited
    function create_content_object()
    {
        $values = $this->exportValues();
        
        $object = new Gender();
        $object->set_question($values[Gender :: PROPERTY_QUESTION]);
        $object->set_instruction($values[Gender :: PROPERTY_INSTRUCTION]);
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $values = $this->exportValues();
        
        $object = $this->get_content_object();
        $object->set_question($values[Gender :: PROPERTY_QUESTION]);
        $object->set_instruction($values[Gender :: PROPERTY_INSTRUCTION]);
        return parent :: update_content_object();
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        $defaults[Gender :: PROPERTY_QUESTION] = $defaults[Gender :: PROPERTY_QUESTION] ==
             null ? $object->get_question() : $defaults[Gender :: PROPERTY_QUESTION];
        $defaults[Gender :: PROPERTY_INSTRUCTION] = $object->get_instruction();
        parent :: setDefaults($defaults);
    }
}

?>
