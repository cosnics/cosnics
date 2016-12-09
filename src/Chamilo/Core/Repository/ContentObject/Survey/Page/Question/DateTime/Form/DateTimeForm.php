<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime\Storage\DataClass\DateTime;
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
class DateTimeForm extends ContentObjectForm
{
    const TAB_GENERAL = 'general';
    const TAB_QUESTION = 'question';

    private static $html_editor_options = array(
        FormValidatorHtmlEditorOptions::OPTION_HEIGHT => '75', 
        FormValidatorHtmlEditorOptions::OPTION_COLLAPSE_TOOLBAR => true);

    /**
     * Prepare all the different tabs
     */
    function prepareTabs()
    {
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime', 
                    true) . 'Form.js'));
        
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self::TAB_QUESTION, 
                Translation::get(
                    (string) StringUtilities::getInstance()->createString(self::TAB_QUESTION)->upperCamelize()), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\DateTime', 
                    'Tab/' . self::TAB_QUESTION), 
                'build_question_form'));
        
        $this->addDefaultTab();
        $this->addMetadataTabs();
    }

    function build_question_form()
    {
        $this->add_textfield(
            DateTime::PROPERTY_QUESTION, 
            Translation::get('Question'), 
            true, 
            array('size' => '100', 'id' => 'question', 'style' => 'width: 95%'));
        $this->add_html_editor(
            DateTime::PROPERTY_INSTRUCTION, 
            Translation::get('Instruction'), 
            false, 
            self::$html_editor_options);
        
        $this->addElement(
            'radio', 
            DateTime::PROPERTY_QUESTION_TYPE, 
            Translation::get('DateQuestion'), 
            null, 
            DateTime::TYPE_DATE);
        $this->addElement(
            'radio', 
            DateTime::PROPERTY_QUESTION_TYPE, 
            Translation::get('TimeQuestion'), 
            null, 
            DateTime::TYPE_TIME);
    }
    
    // Inherited
    function create_content_object()
    {
        $values = $this->exportValues();
        
        $object = new DateTime();
        $object->set_question($values[DateTime::PROPERTY_QUESTION]);
        $object->set_instruction($values[DateTime::PROPERTY_INSTRUCTION]);
        $object->set_question_type($values[DateTime::PROPERTY_QUESTION_TYPE]);
        
        $this->set_content_object($object);
        return parent::create_content_object();
    }

    function update_content_object()
    {
        $values = $this->exportValues();
        
        $object = $this->get_content_object();
        $object->set_question($values[DateTime::PROPERTY_QUESTION]);
        $object->set_instruction($values[DateTime::PROPERTY_INSTRUCTION]);
        $object->set_question_type($values[DateTime::PROPERTY_QUESTION_TYPE]);
        
        return parent::update_content_object();
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        $defaults[DateTime::PROPERTY_QUESTION] = $defaults[DateTime::PROPERTY_QUESTION] == null ? $object->get_question() : $defaults[DateTime::PROPERTY_QUESTION];
        $defaults[DateTime::PROPERTY_INSTRUCTION] = $object->get_instruction();
        $defaults[DateTime::PROPERTY_QUESTION_TYPE] = $object->get_question_type();
        
        parent::setDefaults($defaults);
    }
}

?>
