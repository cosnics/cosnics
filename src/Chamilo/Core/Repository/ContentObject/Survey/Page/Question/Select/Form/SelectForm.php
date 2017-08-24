<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataClass\Select;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataClass\SelectOption;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select\Storage\DataManager;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package repository.content_object.survey_select_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SelectForm extends ContentObjectForm
{
    const TAB_GENERAL = 'general';
    const TAB_QUESTION = 'question';
    const TAB_OPTION = 'option';

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
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select', 
                    true) . 'Form.js'));
        
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self::TAB_QUESTION, 
                Translation::get(
                    (string) StringUtilities::getInstance()->createString(self::TAB_QUESTION)->upperCamelize()), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select', 
                    'Tab/' . self::TAB_QUESTION), 
                'build_question_form'));
        
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self::TAB_OPTION, 
                Translation::get(
                    (string) StringUtilities::getInstance()->createString(self::TAB_OPTION)->upperCamelize()), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Select', 
                    'Tab/' . self::TAB_OPTION), 
                'build_option_form'));
        
        $this->addDefaultTab();
        $this->addMetadataTabs();
    }

    function build_question_form()
    {
        $this->add_textfield(
            Select::PROPERTY_QUESTION, 
            Translation::get('Question'), 
            true, 
            array('size' => '100', 'id' => 'question', 'style' => 'width: 95%'));
        $this->add_html_editor(
            Select::PROPERTY_INSTRUCTION, 
            Translation::get('Instruction'), 
            false, 
            self::$html_editor_options);
    }

    function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            
            $object = $this->get_content_object();
            $defaults[Select::PROPERTY_QUESTION] = $defaults[Select::PROPERTY_QUESTION] == null ? $object->get_question() : $defaults[Select::PROPERTY_QUESTION];
            $defaults[Select::PROPERTY_INSTRUCTION] = $object->get_instruction();
            if ($object->get_number_of_options() != 0)
            {
                $options = $object->get_options();
                
                while ($option = $options->next_result())
                {
                    $defaults[SelectOption::PROPERTY_VALUE . '[' . $option->get_display_order() . ']'] = $option->get_value();
                }
            }
            else
            {
                $number_of_options = intval($_SESSION['select_number_of_options']);
            }
        }
        parent::setDefaults($defaults);
    }

    function create_content_object()
    {
        $values = $this->exportValues();
        
        $object = new Select();
        $object->set_answer_type($_SESSION['select_answer_type']);
        $object->set_question($values[Select::PROPERTY_QUESTION]);
        $object->set_instruction($values[Select::PROPERTY_INSTRUCTION]);
        $this->set_content_object($object);
        $object = parent::create_content_object();
        $this->add_options_to_object();
        return $object;
    }

    function update_content_object()
    {
        $values = $this->exportValues();
        
        $object = $this->get_content_object();
        $object->set_answer_type($_SESSION['select_answer_type']);
        $object->set_question($values[Select::PROPERTY_QUESTION]);
        $object->set_instruction($values[Select::PROPERTY_INSTRUCTION]);
        $this->add_options_to_object();
        return parent::update_content_object();
    }

    function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']) || isset($_POST['change_answer_type']))
        {
            return false;
        }
        return parent::validate();
    }

    function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        
        foreach ($values[SelectOption::PROPERTY_VALUE] as $display_order => $value)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(SelectOption::class_name(), SelectOption::PROPERTY_QUESTION_ID), 
                new StaticConditionVariable($object->get_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(SelectOption::class_name(), SelectOption::PROPERTY_DISPLAY_ORDER), 
                new StaticConditionVariable($display_order));
            $condition = new AndCondition($conditions);
            $option = DataManager::retrieve(SelectOption::class_name(), new DataClassRetrieveParameters($condition));
            
            if ($option)
            {
                $option->set_value($value);
                $succes = $option->update();
            }
            else
            {
                $option = new SelectOption();
                $option->set_value($value);
                $option->set_question_id($object->get_id());
                $option->set_display_order($display_order);
                $option->create();
            }
        }
        
        $options = $_SESSION['mq_skip_options'];
        if (count($options) > 0)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(SelectOption::class_name(), SelectOption::PROPERTY_QUESTION_ID), 
                new StaticConditionVariable($object->get_id()));
            $conditions[] = new InCondition(
                new PropertyConditionVariable(SelectOption::class_name(), SelectOption::PROPERTY_DISPLAY_ORDER), 
                $options);
            $condition = new AndCondition($conditions);
            $options = DataManager::retrieve(SelectOption::class_name(), new DataClassRetrieveParameters($condition));
            while ($option = $options->next_result())
            {
                $option->delete();
            }
        }
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this multiple choice question
     */
    function build_option_form()
    {
        $renderer = $this->defaultRenderer();
        
        if (! $this->isSubmitted())
        {
            unset($_SESSION['select_number_of_options']);
            unset($_SESSION['select_skip_options']);
            unset($_SESSION['select_answer_type']);
        }
        if (! isset($_SESSION['select_number_of_options']))
        {
            $_SESSION['select_number_of_options'] = 3;
        }
        if (! isset($_SESSION['select_skip_options']))
        {
            $_SESSION['select_skip_options'] = array();
        }
        if (! isset($_SESSION['select_answer_type']))
        {
            $_SESSION['select_answer_type'] = 'radio';
        }
        if (isset($_POST['add']))
        {
            $_SESSION['select_number_of_options'] = $_SESSION['select_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['select_skip_options'][] = $indexes[0];
        }
        if (isset($_POST['change_answer_type']))
        {
            $_SESSION['select_answer_type'] = $_SESSION['select_answer_type'] == 'radio' ? 'checkbox' : 'radio';
        }
        $object = $this->get_content_object();
        if (! $this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            $_SESSION['select_number_of_options'] = $object->get_number_of_options();
            $_SESSION['select_answer_type'] = $object->get_answer_type();
        }
        $number_of_options = intval($_SESSION['select_number_of_options']);
        
        if ($_SESSION['select_answer_type'] == 'radio')
        {
            $switch_label = Translation::get('SwitchToMultipleSelect');
        }
        elseif ($_SESSION['select_answer_type'] == 'checkbox')
        {
            $switch_label = Translation::get('SwitchToSingleSelect');
        }
        
        $this->addElement(
            'hidden', 
            'select_answer_type', 
            $_SESSION['select_answer_type'], 
            array('id' => 'select_answer_type'));
        $this->addElement(
            'hidden', 
            'select_number_of_options', 
            $_SESSION['select_number_of_options'], 
            array('id' => 'select_number_of_options'));
        
        $buttons = array();
        // TODO adding fix for multiple select question
        $buttons[] = $this->createElement(
            'style_button', 
            'change_answer_type', 
            $switch_label, 
            array('id' => 'change_answer_type'), 
            null, 
            'retweet');
        // Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when
        // clicking an image button
        $buttons[] = $this->createElement(
            'style_button', 
            'add[]', 
            Translation::get('AddSelectOption'), 
            array('id' => 'add_option'), 
            null, 
            'plus');
        $this->addGroup($buttons, 'question_buttons', null, '', false);
        
        $html_editor_options = array();
        $html_editor_options['style'] = 'width: 100%; height: 65px;';

        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th style="width: 320px;">' . Translation::get('Options') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));
        
        $visual_number = 0;
        
        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['select_skip_options']))
            {
                $group = array();
                
                $visual_number ++;
                $group[] = $this->createElement('static', null, null, $visual_number);
                $group[] = & $this->createElement(
                    'text', 
                    SelectOption::PROPERTY_VALUE . '[' . $option_number . ']', 
                    Translation::get('Answer'), 
                    array('style' => 'width: 300px;'));
                
                if ($number_of_options - count($_SESSION['select_skip_options']) > 2)
                {
                    $group[] = & $this->createElement(
                        'image', 
                        'remove[' . $option_number . ']', 
                        Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                        array('class' => 'remove_option', 'id' => 'remove_' . $option_number));
                }
                else
                {
                    $group[] = & $this->createElement(
                        'static', 
                        null, 
                        null, 
                        '<img class="remove_option" src="' .
                             Theme::getInstance()->getCommonImagePath('Action/DeleteNa') . '" />');
                }
                
                $this->addGroup($group, SelectOption::PROPERTY_VALUE . '_' . $option_number, null, '', false);
                
                $renderer->setElementTemplate(
                    '<tr id="option_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') .
                         '">{element}</tr>', 
                        SelectOption::PROPERTY_VALUE . '_' . $option_number);
                $renderer->setGroupElementTemplate(
                    '<td>{element}</td>', 
                    SelectOption::PROPERTY_VALUE . '_' . $option_number);
            }
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode(PHP_EOL, $table_footer));
        
        $this->addGroup($buttons, 'question_buttons', null, '', false);
        
        $renderer->setElementTemplate(
            '<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 
            'question_buttons');
        $renderer->setGroupElementTemplate(
            '<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 
            'question_buttons');
    }
}