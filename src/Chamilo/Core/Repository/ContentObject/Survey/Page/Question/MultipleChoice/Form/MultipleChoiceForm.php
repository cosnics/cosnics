<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\MultipleChoice;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\MultipleChoiceOption;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Template\TemplateConfiguration;
use Chamilo\Core\Repository\Exception\NoTemplateException;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository\content_object\survey_multiple_choice_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class MultipleChoiceForm extends ContentObjectForm
{
    const TAB_GENERAL = 'general';
    const TAB_QUESTION = 'question';
    const TAB_OPTION = 'option';
    
    // Extra properties for the form
    const PROPERTY_NUMBER_OF_OPTIONS = 'number_of_options';
    const PROPERTY_SKIPPED_OPTIONS = 'skipped_options';
    const PROPERTY_OPTION_BUTTONS = 'options_buttons';
    const PROPERTY_OPTION = 'option';
    const PROPERTY_OPTION_GROUP = 'option_group';
    
    // Several buttons
    const BUTTON_CHANGE_ANSWER_TYPE = 'change_answer_type';
    const BUTTON_CHANGE_DISPLAY_TYPE = 'change_display_type';
    const BUTTON_ADD_OPTION = 'add_option';
    const BUTTON_REMOVE_OPTION = 'remove';

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
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice', 
                    true) . 'Form.js'));
        
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self::TAB_QUESTION, 
                Translation::get(
                    (string) StringUtilities::getInstance()->createString(self::TAB_QUESTION)->upperCamelize()), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice', 
                    'Tab/' . self::TAB_QUESTION), 
                'build_question_form'));
        
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self::TAB_OPTION, 
                Translation::get(
                    (string) StringUtilities::getInstance()->createString(self::TAB_OPTION)->upperCamelize()), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice', 
                    'Tab/' . self::TAB_OPTION), 
                'build_option_form'));
        
        $this->addDefaultTab();
        $this->addMetadataTabs();
    }

    /**
     * Add the question and instruction fields
     * 
     * @throws NoTemplateException
     */
    function build_question_form()
    {
        $this->add_textfield(
            MultipleChoice::PROPERTY_QUESTION, 
            Translation::get('Question'), 
            true, 
            array('size' => '100', 'id' => 'question', 'style' => 'width: 95%'));
        
        $this->add_html_editor(
            MultipleChoice::PROPERTY_INSTRUCTION, 
            Translation::get('Instruction'), 
            false, 
            self::$html_editor_options);
        
        try
        {
            $configuration = $this->get_content_object_template_configuration();
            
            $allowed_to_edit_question = $configuration->get_configuration(
                MultipleChoice::PROPERTY_QUESTION, 
                TemplateConfiguration::ACTION_EDIT);
            
            if (! $allowed_to_edit_question)
            {
                $this->getElement(MultipleChoice::PROPERTY_QUESTION)->freeze();
            }
            
            $allowed_to_edit_instruction = $configuration->get_configuration(
                MultipleChoice::PROPERTY_INSTRUCTION, 
                TemplateConfiguration::ACTION_EDIT);
            
            if (! $allowed_to_edit_instruction)
            {
                $this->getElement(MultipleChoice::PROPERTY_INSTRUCTION)->freeze();
            }
        }
        catch (NoTemplateException $exception)
        {
            throw $exception;
        }
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this multiple choice question
     */
    function build_option_form()
    {
        $content_object = $this->get_content_object();
        $renderer = $this->defaultRenderer();
        
        $this->addElement(
            'hidden', 
            MultipleChoice::PROPERTY_ANSWER_TYPE, 
            null, 
            array('id' => MultipleChoice::PROPERTY_ANSWER_TYPE));
        $this->addElement(
            'hidden', 
            MultipleChoice::PROPERTY_DISPLAY_TYPE, 
            null, 
            array('id' => MultipleChoice::PROPERTY_DISPLAY_TYPE));
        
        $this->addElement(
            'hidden', 
            self::PROPERTY_NUMBER_OF_OPTIONS, 
            null, 
            array('id' => self::PROPERTY_NUMBER_OF_OPTIONS));
        $this->addElement(
            'hidden', 
            self::PROPERTY_SKIPPED_OPTIONS, 
            null, 
            array('id' => self::PROPERTY_SKIPPED_OPTIONS));
        
        $buttons = array();
        
        $answer_type_label = $content_object->get_answer_type() == MultipleChoice::ANSWER_TYPE_CHECKBOX ? 'SwitchToRadioButtons' : 'SwitchToCheckboxes';
        $display_type_label = $content_object->get_display_type() == MultipleChoice::DISPLAY_TYPE_SELECT ? 'SwitchToTable' : 'SwitchToSelect';
        
        $buttons[] = $this->createElement(
            'style_button', 
            self::BUTTON_CHANGE_ANSWER_TYPE . '[]', 
            Translation::get($answer_type_label), 
            array('class' => self::BUTTON_CHANGE_ANSWER_TYPE), 
            null, 
            'retweet');
        
        $buttons[] = $this->createElement(
            'style_button', 
            self::BUTTON_CHANGE_DISPLAY_TYPE . '[]', 
            Translation::get($display_type_label), 
            array('class' => self::BUTTON_CHANGE_DISPLAY_TYPE), 
            null, 
            'retweet');
        
        $buttons[] = $this->createElement(
            'style_button', 
            self::BUTTON_ADD_OPTION . '[]', 
            Translation::get('AddMultipleChoiceOption'), 
            array('class' => self::BUTTON_ADD_OPTION), 
            null, 
            'plus');
        
        $this->addGroup($buttons, self::PROPERTY_OPTION_BUTTONS, null, '', false);
        
        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation::get('Options') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));
        
        $number_of_options = $this->determine_number_of_options();
        $skipped_options = $this->determine_skipped_options();
        
        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $skipped_options))
            {
                $group = array();
                
                $display_order = $this->createElement(
                    'text', 
                    self::PROPERTY_OPTION . '[' . $option_number . ']' . '[' .
                         MultipleChoiceOption::PROPERTY_DISPLAY_ORDER . ']');
                $display_order->freeze();
                
                $group[] = $display_order;
                
                $group[] = $this->createElement(
                    'hidden', 
                    self::PROPERTY_OPTION . '[' . $option_number . ']' . '[' . MultipleChoiceOption::PROPERTY_ID . ']');
                
                $group[] = $this->create_textfield(
                    self::PROPERTY_OPTION . '[' . $option_number . ']' . '[' . MultipleChoiceOption::PROPERTY_VALUE . ']', 
                    Translation::get('Answer'), 
                    array('size' => '100', 'style' => 'width: 95%'));
                
                if ($number_of_options > 2)
                {
                    $group[] = & $this->createElement(
                        'style_button', 
                        self::BUTTON_REMOVE_OPTION . '[' . $option_number . ']', 
                        Translation::get('RemoveOption'), 
                        array(
                            'class' => 'remove_option mini btn-danger ' . self::BUTTON_REMOVE_OPTION, 
                            'id' => 'remove_' . $option_number), 
                        null, 
                        'remove');
                }
                else
                {
                    $group[] = & $this->createElement(
                        'style_button', 
                        self::BUTTON_REMOVE_OPTION . '[' . $option_number . ']', 
                        Translation::get('RemoveOption'), 
                        array('class' => 'remove_option mini btn-danger ' . self::BUTTON_REMOVE_OPTION), 
                        null, 
                        'remove');
                }
                
                $this->addGroup($group, self::PROPERTY_OPTION_GROUP . '[' . $option_number . ']', null, '', false);
                
                $renderer->setElementTemplate(
                    '<tr id="option_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') .
                         '">{element}</tr>', 
                        self::PROPERTY_OPTION_GROUP . '[' . $option_number . ']');
                $renderer->setGroupElementTemplate(
                    '<td>{element}</td>', 
                    self::PROPERTY_OPTION_GROUP . '[' . $option_number . ']');
            }
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode(PHP_EOL, $table_footer));
        
        $this->addGroup($buttons, self::PROPERTY_OPTION_BUTTONS, null, '', false);
        
        $renderer->setElementTemplate(
            '<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 
            self::PROPERTY_OPTION_BUTTONS);
        $renderer->setGroupElementTemplate(
            '<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 
            self::PROPERTY_OPTION_BUTTONS);
        
        try
        {
            $configuration = $this->get_content_object_template_configuration();
            
            $allowed_to_edit_answer_type = $configuration->get_configuration(
                MultipleChoice::PROPERTY_ANSWER_TYPE, 
                TemplateConfiguration::ACTION_EDIT);
            
            if (! $allowed_to_edit_answer_type)
            {
                $this->get_group_element(self::PROPERTY_OPTION_BUTTONS, self::BUTTON_CHANGE_ANSWER_TYPE . '[]')->freeze();
            }
            
            $allowed_to_edit_display_type = $configuration->get_configuration(
                MultipleChoice::PROPERTY_DISPLAY_TYPE, 
                TemplateConfiguration::ACTION_EDIT);
            
            if (! $allowed_to_edit_display_type)
            {
                $this->get_group_element(self::PROPERTY_OPTION_BUTTONS, self::BUTTON_CHANGE_DISPLAY_TYPE . '[]')->freeze();
            }
            
            $allowed_to_edit_options = $configuration->get_configuration(
                MultipleChoice::PROPERTY_OPTIONS, 
                TemplateConfiguration::ACTION_EDIT);
            
            if (! $allowed_to_edit_options)
            {
                $this->get_group_element(self::PROPERTY_OPTION_BUTTONS, self::BUTTON_ADD_OPTION . '[]')->freeze();
                
                for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $this->get_group_element(
                        self::PROPERTY_OPTION_GROUP . '[' . $option_number . ']', 
                        self::PROPERTY_OPTION . '[' . $option_number . ']' . '[' . MultipleChoiceOption::PROPERTY_VALUE .
                             ']')->freeze();
                    $this->get_group_element(
                        self::PROPERTY_OPTION_GROUP . '[' . $option_number . ']', 
                        self::BUTTON_REMOVE_OPTION . '[' . $option_number . ']')->freeze();
                }
            }
        }
        catch (NoTemplateException $exception)
        {
            throw $exception;
        }
    }

    /*
     * (non-PHPdoc) @see \repository\ContentObjectForm::setDefaults()
     */
    function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            
            $defaults[MultipleChoice::PROPERTY_QUESTION] = $object->get_question();
            $defaults[MultipleChoice::PROPERTY_INSTRUCTION] = $object->get_instruction();
            
            if ($object->get_number_of_options())
            {
                $options = $object->get_options();
                $option_number = 0;
                
                foreach ($options as $key => $option)
                {
                    $defaults[self::PROPERTY_OPTION . '[' . $option_number . ']' . '[' .
                         MultipleChoiceOption::PROPERTY_DISPLAY_ORDER . ']'] = $option->get_display_order();
                    
                    $defaults[self::PROPERTY_OPTION . '[' . $option_number . ']' . '[' .
                         MultipleChoiceOption::PROPERTY_ID . ']'] = $option->get_id();
                    $defaults[self::PROPERTY_OPTION . '[' . $option_number . ']' . '[' .
                         MultipleChoiceOption::PROPERTY_VALUE . ']'] = $option->get_value();
                    $option_number ++;
                }
            }
        }
        else
        {
            $values = $this->exportValues();
            $options = $values[self::PROPERTY_OPTION];
            
            $display_order = 1;
            
            $number_of_options = $this->determine_number_of_options();
            $skipped_options = $this->determine_skipped_options();
            
            for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
            {
                if (! in_array($option_number, $skipped_options))
                {
                    $defaults[self::PROPERTY_OPTION . '[' . $option_number . ']' . '[' .
                         MultipleChoiceOption::PROPERTY_DISPLAY_ORDER . ']'] = $display_order;
                    unset(
                        $this->_submitValues[self::PROPERTY_OPTION][$option_number][MultipleChoiceOption::PROPERTY_DISPLAY_ORDER]);
                    $display_order ++;
                }
            }
        }
        
        $defaults[self::PROPERTY_NUMBER_OF_OPTIONS] = $this->determine_number_of_options();
        $defaults[self::PROPERTY_SKIPPED_OPTIONS] = $this->determine_skipped_options(true);
        $defaults[MultipleChoice::PROPERTY_ANSWER_TYPE] = $this->determine_answer_type();
        $defaults[MultipleChoice::PROPERTY_DISPLAY_TYPE] = $this->determine_display_type();
        
        parent::setDefaults($defaults);
    }

    /**
     * Process the question options and create / delete / update them accordingly
     */
    function process_options()
    {
        $values = $this->exportValues();
        
        $content_object = $this->get_content_object();
        $current_options = $content_object->get_options();
        
        $new_options = array();
        $posted_options = array();
        
        foreach ($values[self::PROPERTY_OPTION] as $key => $option_properties)
        {
            $posted_option = new MultipleChoiceOption();
            
            if ($option_properties[MultipleChoiceOption::PROPERTY_ID])
            {
                $posted_option->set_id($option_properties[MultipleChoiceOption::PROPERTY_ID]);
            }
            
            $posted_option->set_value($option_properties[MultipleChoiceOption::PROPERTY_VALUE]);
            $posted_option->set_display_order($option_properties[MultipleChoiceOption::PROPERTY_DISPLAY_ORDER]);
            $posted_option->set_question_id($content_object->get_id());
            
            if ($posted_option->get_id())
            {
                $posted_options[$posted_option->get_id()] = $posted_option;
            }
            else
            {
                $new_options[] = $posted_option;
            }
        }
        
        /**
         * Options which should be deleted
         */
        $current_option_ids = array();
        
        foreach ($current_options as $current_option)
        {
            $current_option_ids[$current_option->get_id()] = $current_option;
        }
        
        $ids_to_delete = array_diff(array_keys($current_option_ids), array_keys($posted_options));
        
        foreach ($ids_to_delete as $id_to_delete)
        {
            $current_option_ids[$id_to_delete]->delete();
        }
        
        /**
         * Options which should be added
         */
        
        foreach ($new_options as $new_option)
        {
            $new_option->create();
        }
        
        /**
         * Options which should be updated
         */
        $ids_to_update = array_intersect(array_keys($current_option_ids), array_keys($posted_options));
        
        foreach ($ids_to_update as $id_to_update)
        {
            $posted_options[$id_to_update]->update();
        }
    }

    /*
     * (non-PHPdoc) @see \repository\ContentObjectForm::create_content_object()
     */
    function create_content_object()
    {
        $object = $this->fill_content_object(new MultipleChoice());
        $this->set_content_object($object);
        $object = parent::create_content_object();
        
        $this->process_options();
        
        return $object;
    }

    /*
     * (non-PHPdoc) @see \repository\ContentObjectForm::update_content_object()
     */
    function update_content_object()
    {
        $object = $this->fill_content_object($this->get_content_object());
        $this->process_options();
        
        return parent::update_content_object();
    }

    /**
     *
     * @param MultipleChoice $object
     * @return MultipleChoice
     */
    private function fill_content_object(MultipleChoice $object)
    {
        $values = $this->exportValues();
        
        $object->set_answer_type($values[MultipleChoice::PROPERTY_ANSWER_TYPE]);
        $object->set_display_type($values[MultipleChoice::PROPERTY_DISPLAY_TYPE]);
        $object->set_question($values[MultipleChoice::PROPERTY_QUESTION]);
        $object->set_instruction($values[MultipleChoice::PROPERTY_INSTRUCTION]);
        
        return $object;
    }

    /**
     *
     * @return int
     */
    function determine_number_of_options()
    {
        if (! $this->get_content_object()->get_number_of_options())
        {
            $number_of_options = $this->isSubmitted() ? (int) $this->exportValue(self::PROPERTY_NUMBER_OF_OPTIONS) : 3;
        }
        else
        {
            if ($this->isSubmitted())
            {
                $number_of_options = (int) $this->exportValue(self::PROPERTY_NUMBER_OF_OPTIONS);
            }
            else
            {
                $number_of_options = $this->get_content_object()->get_number_of_options();
            }
        }
        
        if (Request::post(self::BUTTON_ADD_OPTION))
        {
            $number_of_options ++;
            unset($this->_submitValues[self::PROPERTY_NUMBER_OF_OPTIONS]);
        }
        
        return $number_of_options;
    }

    /**
     *
     * @param boolean $serialize
     * @return string, multitype:int
     */
    function determine_skipped_options($serialize = false)
    {
        $skipped_options = $this->exportValue(self::PROPERTY_SKIPPED_OPTIONS);
        $skipped_options = $skipped_options ? unserialize($skipped_options) : array();
        
        if (Request::post(self::BUTTON_REMOVE_OPTION))
        {
            $options_to_skip = Request::post(self::BUTTON_REMOVE_OPTION);
            
            foreach (array_keys($options_to_skip) as $skipped_id)
            {
                if (! in_array($skipped_id, $skipped_options))
                {
                    $skipped_options[] = $skipped_id;
                }
            }
            
            unset($this->_submitValues[self::PROPERTY_SKIPPED_OPTIONS]);
        }
        
        return $serialize ? serialize($skipped_options) : $skipped_options;
    }

    /**
     *
     * @return int
     */
    function determine_answer_type()
    {
        if ($this->get_content_object()->get_answer_type())
        {
            $answer_type = $this->get_content_object()->get_answer_type();
        }
        else
        {
            $answer_type = MultipleChoice::ANSWER_TYPE_CHECKBOX;
        }
        
        if (Request::post(self::BUTTON_CHANGE_ANSWER_TYPE))
        {
            unset($this->_submitValues[MultipleChoice::PROPERTY_ANSWER_TYPE]);
            $answer_type = ($answer_type == MultipleChoice::ANSWER_TYPE_RADIO ? MultipleChoice::ANSWER_TYPE_CHECKBOX : MultipleChoice::ANSWER_TYPE_RADIO);
        }
        
        return $answer_type;
    }

    /**
     *
     * @return int
     */
    function determine_display_type()
    {
        if ($this->get_content_object()->get_display_type())
        {
            $display_type = $this->get_content_object()->get_display_type();
        }
        else
        {
            $display_type = MultipleChoice::DISPLAY_TYPE_SELECT;
        }
        
        if (Request::post(self::BUTTON_CHANGE_DISPLAY_TYPE))
        {
            unset($this->_submitValues[MultipleChoice::PROPERTY_DISPLAY_TYPE]);
            $display_type = ($display_type == MultipleChoice::DISPLAY_TYPE_SELECT ? MultipleChoice::DISPLAY_TYPE_TABLE : MultipleChoice::DISPLAY_TYPE_SELECT);
        }
        
        return $display_type;
    }

    /*
     * (non-PHPdoc) @see \repository\ContentObjectForm::validate()
     */
    public function validate()
    {
        if (Request::post(self::BUTTON_CHANGE_ANSWER_TYPE) || Request::post(self::BUTTON_CHANGE_DISPLAY_TYPE) ||
             Request::post(self::BUTTON_ADD_OPTION) || Request::post(self::BUTTON_REMOVE_OPTION))
        {
            return false;
        }
        
        return parent::validate();
    }
}