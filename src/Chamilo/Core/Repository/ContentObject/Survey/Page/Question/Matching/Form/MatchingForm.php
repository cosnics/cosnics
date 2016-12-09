<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Form;

use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataClass\Matching;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataClass\MatchingMatch;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataClass\MatchingOption;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataManager;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.content_object.survey_matching_question
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
class MatchingForm extends ContentObjectForm
{
    const TAB_GENERAL = 'general';
    const TAB_QUESTION = 'question';
    const TAB_OPTION = 'option';
    const TAB_MATCH = 'match';

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
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching', 
                    true) . 'Form.js'));
        
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self::TAB_QUESTION, 
                Translation::get(
                    (string) StringUtilities::getInstance()->createString(self::TAB_QUESTION)->upperCamelize()), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching', 
                    'Tab/' . self::TAB_QUESTION), 
                'build_question_form'));
        
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self::TAB_OPTION, 
                Translation::get(
                    (string) StringUtilities::getInstance()->createString(self::TAB_OPTION)->upperCamelize()), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching', 
                    'Tab/' . self::TAB_OPTION), 
                'build_option_form'));
        
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self::TAB_MATCH, 
                Translation::get(
                    (string) StringUtilities::getInstance()->createString(self::TAB_MATCH)->upperCamelize()), 
                Theme::getInstance()->getImagePath(
                    'Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching', 
                    'Tab/' . self::TAB_MATCH), 
                'build_match_form'));
        
        $this->addDefaultTab();
        $this->addMetadataTabs();
    }

    /**
     * Initialize the general form based on the form type
     */
    function build_general_form()
    {
        if ($this->get_form_type() == self::TYPE_CREATE)
        {
            parent::build_creation_form(self::$html_editor_options, true);
        }
        elseif ($this->get_form_type() == self::TYPE_EDIT)
        {
            parent::build_editing_form(self::$html_editor_options, true);
        }
    }

    function build_question_form()
    {
        $this->add_textfield(
            Matching::PROPERTY_QUESTION, 
            Translation::get('Question'), 
            true, 
            array('size' => '100', 'id' => 'question', 'style' => 'width: 95%'));
        $this->add_html_editor(
            Matching::PROPERTY_INSTRUCTION, 
            Translation::get('Instruction'), 
            false, 
            self::$html_editor_options);
    }

    /**
     * Adds the options and matches to the form
     */
    function build_option_form()
    {
        $this->update_number_of_options_and_matches();
        $this->add_options();
    }

    /**
     * Adds the options and matches to the form
     */
    function build_match_form()
    {
        $this->update_number_of_options_and_matches();
        $this->add_matches();
    }

    function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        $defaults[Matching::PROPERTY_QUESTION] = $defaults[Matching::PROPERTY_QUESTION] == null ? $object->get_question() : $defaults[Matching::PROPERTY_QUESTION];
        $defaults[Matching::PROPERTY_INSTRUCTION] = $object->get_instruction();
        
        if ($object->get_number_of_options() != 0)
            
            if ($object->get_number_of_options() != 0)
            {
                $options = $object->get_options();
                
                while ($option = $options->next_result())
                {
                    $defaults[MatchingOption::PROPERTY_VALUE . '[' . ($option->get_display_order() - 1) . ']'] = $option->get_value();
                }
                
                $matches = $object->get_matches();
                
                while ($match = $matches->next_result())
                {
                    $defaults[MatchingMatch::PROPERTY_VALUE . '[' . ($match->get_display_order() - 1) . ']'] = $match->get_value();
                }
            }
            else
            {
                $number_of_options = intval($_SESSION['mq_number_of_options']);
            }
        
        parent::setDefaults($defaults);
    }

    function create_content_object()
    {
        $values = $this->exportValues();
        
        $object = new Matching();
        $object->set_question($values[Matching::PROPERTY_QUESTION]);
        $object->set_instruction($values[Matching::PROPERTY_INSTRUCTION]);
        $this->set_content_object($object);
        $object = parent::create_content_object();
        $this->add_answers();
        return $object;
    }

    function update_content_object()
    {
        $values = $this->exportValues();
        $object = $this->get_content_object();
        $object->set_question($values[Matching::PROPERTY_QUESTION]);
        $object->set_instruction($values[Matching::PROPERTY_INSTRUCTION]);
        $this->add_answers();
        return parent::update_content_object();
    }

    /**
     * Adds the answer to the current learning object.
     * This function adds the list of possible options and matches and
     * the relation between the options and the matches to the question.
     */
    function add_answers()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        
        foreach ($values[MatchingOption::PROPERTY_VALUE] as $display_order => $value)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(MatchingOption::class_name(), MatchingOption::PROPERTY_QUESTION_ID), 
                new StaticConditionVariable($object->get_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(MatchingOption::class_name(), MatchingOption::PROPERTY_DISPLAY_ORDER), 
                new StaticConditionVariable($display_order + 1));
            $condition = new AndCondition($conditions);
            
            $option = DataManager::retrieve(MatchingOption::class_name(), new DataClassRetrieveParameters($condition));
            
            if ($option instanceof MatchingOption)
            {
                $option->set_value($value);
                $succes = $option->update();
            }
            else
            {
                $option = new MatchingOption();
                $option->set_value($value);
                $option->set_question_id($object->get_id());
                $option->set_display_order($display_order + 1);
                $option->create();
            }
        }
        
        $skip_options = $_SESSION['mq_skip_options'];
        
        if (count($skip_options) > 0)
        {
            $orders = array();
            
            foreach ($skip_options as $skip_option)
            {
                $orders[] = $skip_option + 1;
            }
            
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(MatchingOption::class_name(), MatchingOption::PROPERTY_QUESTION_ID), 
                new StaticConditionVariable($object->get_id()));
            $conditions[] = new InCondition(
                new PropertyConditionVariable(MatchingOption::class_name(), MatchingOption::PROPERTY_DISPLAY_ORDER), 
                $orders);
            $condition = new AndCondition($conditions);
            
            $options = DataManager::retrieves(
                MatchingOption::class_name(), 
                new DataClassRetrievesParameters($condition));
            
            while ($option = $options->next_result())
            {
                $option->delete();
            }
        }
        
        foreach ($values[MatchingMatch::PROPERTY_VALUE] as $display_order => $value)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(MatchingMatch::class_name(), MatchingMatch::PROPERTY_QUESTION_ID), 
                new StaticConditionVariable($object->get_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(MatchingMatch::class_name(), MatchingMatch::PROPERTY_DISPLAY_ORDER), 
                new StaticConditionVariable($display_order + 1));
            $condition = new AndCondition($conditions);
            
            $match = DataManager::retrieve(MatchingMatch::class_name(), new DataClassRetrieveParameters($condition));
            
            if ($match instanceof MatchingMatch)
            {
                $match->set_value($value);
                $match->update();
            }
            else
            {
                $match = new MatchingMatch();
                $match->set_value($value);
                $match->set_question_id($object->get_id());
                $match->set_display_order($display_order + 1);
                $match->create();
            }
        }
        
        $skip_matches = $_SESSION['mq_skip_matches'];
        
        if (count($skip_matches) > 0)
        {
            $orders = array();
            
            foreach ($skip_matches as $skip_match)
            {
                $orders[] = $skip_match + 1;
            }
            
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(MatchingMatch::class_name(), MatchingMatch::PROPERTY_QUESTION_ID), 
                new StaticConditionVariable($object->get_id()));
            $conditions[] = new InCondition(
                new PropertyConditionVariable(MatchingMatch::class_name(), MatchingMatch::PROPERTY_DISPLAY_ORDER), 
                $orders);
            $condition = new AndCondition($conditions);
            
            $matches = DataManager::retrieves(MatchingMatch::class_name(), new DataClassRetrievesParameters($condition));
            
            while ($match = $matches->next_result())
            {
                $match->delete();
            }
        }
        
        return true;
    }

    function validate()
    {
        if (isset($_POST['add_match']) || isset($_POST['remove_match']) || isset($_POST['remove_option']) ||
             isset($_POST['add_option']))
        {
            return false;
        }
        return parent::validate();
    }

    /**
     * Updates the session variables to keep track of the current number of options and matches.
     * 
     * @todo This code needs some cleaning :)
     */
    function update_number_of_options_and_matches()
    {
        if (! $this->isSubmitted())
        {
            unset($_SESSION['mq_number_of_options']);
            unset($_SESSION['mq_skip_options']);
            unset($_SESSION['mq_number_of_matches']);
            unset($_SESSION['mq_skip_matches']);
        }
        
        if (! isset($_SESSION['mq_number_of_options']))
        {
            $_SESSION['mq_number_of_options'] = 3;
        }
        
        if (! isset($_SESSION['mq_skip_options']))
        {
            $_SESSION['mq_skip_options'] = array();
        }
        
        if (isset($_POST['add_option']))
        {
            $_SESSION['mq_number_of_options'] = $_SESSION['mq_number_of_options'] + 1;
        }
        
        if (isset($_POST['remove_option']))
        {
            $indexes = array_keys($_POST['remove_option']);
            $_SESSION['mq_skip_options'][] = $indexes[0];
        }
        
        if (! isset($_SESSION['mq_number_of_matches']))
        {
            $_SESSION['mq_number_of_matches'] = 3;
        }
        
        if (! isset($_SESSION['mq_skip_matches']))
        {
            $_SESSION['mq_skip_matches'] = array();
        }
        
        if (isset($_POST['add_match']))
        {
            $_SESSION['mq_number_of_matches'] = $_SESSION['mq_number_of_matches'] + 1;
        }
        
        if (isset($_POST['remove_match']))
        {
            $indexes = array_keys($_POST['remove_match']);
            $_SESSION['mq_skip_matches'][] = $indexes[0];
        }
        
        $object = $this->get_content_object();
        if (! $this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            $_SESSION['mq_number_of_options'] = $object->get_number_of_options();
            $_SESSION['mq_number_of_matches'] = $object->get_number_of_matches();
        }
        
        $this->addElement(
            'hidden', 
            'mq_number_of_options', 
            $_SESSION['mq_number_of_options'], 
            array('id' => 'mq_number_of_options'));
        $this->addElement(
            'hidden', 
            'mq_number_of_matches', 
            $_SESSION['mq_number_of_matches'], 
            array('id' => 'mq_number_of_matches'));
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this multiple choice question
     * 
     * @todo Add rules to require options and matches
     */
    function add_options()
    {
        $number_of_options = intval($_SESSION['mq_number_of_options']);
        
        $this->addElement('category', Translation::get('Options'));
        
        $buttons = array();
        $buttons[] = $this->createElement(
            'style_button', 
            'add_option[]', 
            Translation::get('AddMatchingOption'), 
            array('id' => 'add_option'), 
            null, 
            'plus');
        $this->addGroup($buttons, 'question_buttons', null, '', false);
        
        $renderer = $this->defaultRenderer();
        
        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data options">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation::get('Options') . '</th>';
        // $table_header[] = '<th class="code">' . Translation :: get('Matches') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));
        
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
        
        $visual_number = 0;
        
        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            $group = array();
            if (! in_array($option_number, $_SESSION['mq_skip_options']))
            {
                $visual_number ++;
                $group[] = $this->createElement('static', null, null, $visual_number);
                $group[] = $this->create_html_editor(
                    MatchingOption::PROPERTY_VALUE . '[' . $option_number . ']', 
                    '', 
                    $html_editor_options);
                
                if ($number_of_options - count($_SESSION['mq_skip_options']) > 1)
                {
                    $group[] = $this->createElement(
                        'image', 
                        'remove_option[' . $option_number . ']', 
                        Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                        array('class' => 'remove_option', 'id' => 'remove_option_' . $option_number));
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
                
                $this->addGroup($group, MatchingOption::PROPERTY_VALUE . '_' . $option_number, null, '', false);
                
                $renderer->setElementTemplate(
                    '<tr id="option_' . $option_number . '" class="' . ($visual_number % 2 == 0 ? 'row_odd' : 'row_even') .
                         '">{element}</tr>', 
                        MatchingOption::PROPERTY_VALUE . '_' . $option_number);
                $renderer->setGroupElementTemplate(
                    '<td>{element}</td>', 
                    MatchingOption::PROPERTY_VALUE . '_' . $option_number);
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
        
        $this->addElement('category');
    }

    /**
     * Adds the form-fields to the form to provide the possible matches for this matching question
     */
    function add_matches()
    {
        $number_of_matches = intval($_SESSION['mq_number_of_matches']);
        $this->addElement('category', Translation::get('Matches'));
        
        $buttons = array();
        $buttons[] = $this->createElement(
            'style_button', 
            'add_match[]', 
            Translation::get('AddMatch'), 
            array('id' => 'add_match'), 
            null, 
            'plus');
        $this->addGroup($buttons, 'question_buttons', null, '', false);
        
        $renderer = $this->defaultRenderer();
        
        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data matches">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation::get('Matches') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));
        
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
        
        $label = 'A';
        for ($match_number = 0; $match_number < $number_of_matches; $match_number ++)
        {
            $group = array();
            
            if (! in_array($match_number, $_SESSION['mq_skip_matches']))
            {
                $defaults['match_label'][$match_number] = $label ++;
                $element = $this->createElement(
                    'text', 
                    'match_label[' . $match_number . ']', 
                    Translation::get('Match'), 
                    'style="width: 90%;" ');
                $element->freeze();
                $group[] = $element;
                $group[] = $this->create_html_editor(
                    MatchingMatch::PROPERTY_VALUE . '[' . $match_number . ']', 
                    Translation::get('Match'), 
                    $html_editor_options);
                
                if ($number_of_matches - count($_SESSION['mq_skip_matches']) > 2)
                {
                    $group[] = $this->createElement(
                        'image', 
                        'remove_match[' . $match_number . ']', 
                        Theme::getInstance()->getCommonImagePath('Action/Delete'), 
                        array('class' => 'remove_match', 'id' => 'remove_match_' . $match_number));
                }
                else
                {
                    $group[] = & $this->createElement(
                        'static', 
                        null, 
                        null, 
                        '<img class="remove_match" src="' . Theme::getInstance()->getCommonImagePath('Action/DeleteNa') .
                             '" />');
                }
                
                $this->addGroup($group, MatchingMatch::PROPERTY_VALUE . '_' . $match_number, null, '', false);
                
                $renderer->setElementTemplate(
                    '<tr id="match_' . $match_number . '" class="' .
                         ($match_number - 1 % 2 == 0 ? 'row_odd' : 'row_even') . '">{element}</tr>', 
                        MatchingMatch::PROPERTY_VALUE . '_' . $match_number);
                $renderer->setGroupElementTemplate(
                    '<td>{element}</td>', 
                    MatchingMatch::PROPERTY_VALUE . '_' . $match_number);
                
                $this->addGroupRule(
                    MatchingMatch::PROPERTY_VALUE . '_' . $match_number, 
                    array(
                        MatchingMatch::PROPERTY_VALUE . '[' . $match_number . ']' => array(
                            array(
                                Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 
                                'required'))));
            }
            
            $this->setConstants($defaults);
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
        
        $this->addElement('category');
    }
}
?>