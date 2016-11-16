<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating\Storage\DataClass\Rating;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.content_object.survey_rating_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents a form to create or update open questions
 */
class RatingForm extends ContentObjectForm
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
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating', 
                    true) . 'Form.js'));
        
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self::TAB_QUESTION, 
                Translation::get(
                    (string) StringUtilities::getInstance()->createString(self::TAB_QUESTION)->upperCamelize()), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Rating', 
                    'Tab/' . self::TAB_QUESTION), 
                'build_question_form'));
        
        $this->addDefaultTab();
        $this->addMetadataTabs();
    }

    function build_question_form()
    {
        $this->add_textfield(
            Rating::PROPERTY_QUESTION, 
            Translation::get('Question'), 
            true, 
            array('size' => '100', 'id' => 'question', 'style' => 'width: 95%'));
        $this->add_html_editor(
            Rating::PROPERTY_INSTRUCTION, 
            Translation::get('Instruction'), 
            false, 
            self::$html_editor_options);
        
        $elem[] = $this->createElement(
            'radio', 
            'ratingtype', 
            null, 
            Translation::get('Percentage') . ' (0-100)', 
            0, 
            array('onclick' => 'javascript:hide_controls(\'buttons\')'));
        $elem[] = $this->createElement(
            'radio', 
            'ratingtype', 
            null, 
            Translation::get('Rating'), 
            1, 
            array('onclick' => 'javascript:show_controls(\'buttons\')'));
        $this->addGroup($elem, 'type', Translation::get('SurveyRatingType'), '', false);
        
        $this->addElement('html', '<div style="margin-left: 25px; display: block;" id="buttons">');
        $ratings[] = $this->createElement(
            'text', 
            Rating::PROPERTY_LOW, 
            null, 
            array('class' => 'rating_question_low_value', 'style' => 'width: 124px; margin-right: 4px;'));
        $ratings[] = $this->createElement(
            'text', 
            Rating::PROPERTY_HIGH, 
            null, 
            array('class' => 'rating_question_high_value', 'style' => 'width: 124px;'));
        $this->addGroup($ratings, 'ratings', null, '', false);
        $this->addElement('html', '</div>');
        
        $this->addElement(
            'html', 
            "<script type=\"text/javascript\">
			/* <![CDATA[ */
			hide_controls('buttons');
			function show_controls(elem) {
				el = document.getElementById(elem);
				el.style.display='';
			}
			function hide_controls(elem) {
				el = document.getElementById(elem);
				el.style.display='none';
			}
			/* ]]> */
				</script>\n");
        
        $this->addGroupRule(
            'ratings', 
            array(
                Rating::PROPERTY_LOW => array(array(Translation::get('ValueShouldBeNumeric'), 'numeric')), 
                Rating::PROPERTY_HIGH => array(array(Translation::get('ValueShouldBeNumeric'), 'numeric'))));
        $this->setDefaults();
    }

    function create_content_object()
    {
        $values = $this->exportValues();
        $object = new Rating();
        $object->set_question($values[Rating::PROPERTY_QUESTION]);
        $object->set_instruction($values[Rating::PROPERTY_INSTRUCTION]);
        if (isset($values[Rating::PROPERTY_LOW]) && $values[Rating::PROPERTY_LOW] != '')
            $object->set_low($values[Rating::PROPERTY_LOW]);
        else
            $object->set_low(0);
        
        if (isset($values[Rating::PROPERTY_HIGH]) && $values[Rating::PROPERTY_HIGH] != '')
            $object->set_high($values[Rating::PROPERTY_HIGH]);
        else
            $object->set_high(100);
        
        $this->set_content_object($object);
        return parent::create_content_object();
    }

    function update_content_object()
    {
        $values = $this->exportValues();
        $object = parent::get_content_object();
        $object->set_question($values[Rating::PROPERTY_QUESTION]);
        $object->set_instruction($values[Rating::PROPERTY_INSTRUCTION]);
        if (isset($values[Rating::PROPERTY_LOW]) && $values[Rating::PROPERTY_LOW] != '')
            $object->set_low($values[Rating::PROPERTY_LOW]);
        else
            $object->set_low(0);
        
        if (isset($values[Rating::PROPERTY_HIGH]) && $values[Rating::PROPERTY_HIGH] != '')
            $object->set_high($values[Rating::PROPERTY_HIGH]);
        else
            $object->set_high(100);
        
        $this->set_content_object($object);
        return parent::update_content_object();
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        $defaults[Rating::PROPERTY_QUESTION] = $defaults[Rating::PROPERTY_QUESTION] == null ? $object->get_question() : $defaults[Rating::PROPERTY_QUESTION];
        $defaults[Rating::PROPERTY_INSTRUCTION] = $object->get_instruction();
        
        if ($object != null)
        {
            $defaults[Rating::PROPERTY_LOW] = $object->get_low();
            $defaults[Rating::PROPERTY_HIGH] = $object->get_high();
            
            if ($object->get_low() == 0 && $object->get_high() == 100)
            {
                $defaults['ratingtype'] = 0;
            }
            elseif ($object->get_low() == null && $object->get_high() == null)
            {
                $defaults['ratingtype'] = 0;
            }
            else
            {
                $defaults['ratingtype'] = 1;
            }
        }
        else
        {
            $defaults['ratingtype'] = 0;
        }
        
        parent::setDefaults($defaults);
    }
}
?>