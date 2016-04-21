<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Form;

use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestionOption;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: assessment_select_question_form.class.php $
 * 
 * @package repository.lib.content_object.select_question
 */
class AssessmentSelectQuestionForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Options'));
        $this->add_options();
        $this->addElement('category');
        
        $this->addElement('category', Translation :: get('Hint'));
        
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '100';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
        
        $renderer = $this->defaultRenderer();
        $this->add_html_editor(
            AssessmentSelectQuestion :: PROPERTY_HINT, 
            Translation :: get('Hint', array(), ClassnameUtilities :: getInstance()->getNamespaceFromObject($this)), 
            false, 
            $html_editor_options);
        $renderer->setElementTemplate('{element}<div class="clear"></div>', AssessmentSelectQuestion :: PROPERTY_HINT);
        $this->addElement('category');
        
        $this->addElement(
            'html', 
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion', 
                    true) . 'AssessmentSelectQuestion.js'));
        
        $this->add_example_box();
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Options'));
        $this->add_options();
        $this->addElement('category');
        
        $this->addElement('category', Translation :: get('Hint'));
        
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '100';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';
        
        $renderer = $this->defaultRenderer();
        $this->add_html_editor(
            AssessmentSelectQuestion :: PROPERTY_HINT, 
            Translation :: get('Hint', array(), ClassnameUtilities :: getInstance()->getNamespaceFromObject($this)), 
            false, 
            $html_editor_options);
        $renderer->setElementTemplate('{element}<div class="clear"></div>', AssessmentSelectQuestion :: PROPERTY_HINT);
        $this->addElement('category');
        
        $this->addElement(
            'html', 
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion', 
                    true) . 'AssessmentSelectQuestion.js'));
        
        $this->add_example_box();
    }

    public function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            $defaults[AssessmentSelectQuestion :: PROPERTY_HINT] = $object->get_hint();
            if ($object->get_number_of_options() != 0)
            {
                $options = $object->get_options();
                foreach ($options as $index => $option)
                {
                    $defaults[AssessmentSelectQuestionOption :: PROPERTY_VALUE][$index] = $option->get_value();
                    $defaults[AssessmentSelectQuestionOption :: PROPERTY_SCORE][$index] = $option->get_score();
                    $defaults[AssessmentSelectQuestionOption :: PROPERTY_FEEDBACK][$index] = $option->get_feedback();
                    if ($object->get_answer_type() == 'checkbox')
                    {
                        $defaults[AssessmentSelectQuestionOption :: PROPERTY_CORRECT][$index] = $option->is_correct();
                    }
                    elseif ($option->is_correct())
                    {
                        $defaults[AssessmentSelectQuestionOption :: PROPERTY_CORRECT] = $index;
                    }
                }
            }
            else
            {
                $number_of_options = intval($_SESSION['select_number_of_options']);
                
                for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults[AssessmentSelectQuestionOption :: PROPERTY_SCORE][$option_number] = 0;
                }
            }
        }
        parent :: setDefaults($defaults);
    }

    public function create_content_object()
    {
        $object = new AssessmentSelectQuestion();
        $object->set_hint($this->exportValue(AssessmentSelectQuestion :: PROPERTY_HINT));
        $this->set_content_object($object);
        $this->add_options_to_object();
        return parent :: create_content_object();
    }

    public function update_content_object()
    {
        $this->get_content_object()->set_hint($this->exportValue(AssessmentSelectQuestion :: PROPERTY_HINT));
        $this->add_options_to_object();
        return parent :: update_content_object();
    }

    public function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']) || isset($_POST['change_answer_type']))
        {
            return false;
        }
        return parent :: validate();
    }

    public function validate_selected_answers($fields)
    {
        if (! isset($fields[AssessmentSelectQuestionOption :: PROPERTY_CORRECT]))
        {
            $message = $_SESSION['select_answer_type'] == 'checkbox' ? Translation :: get(
                'SelectAtLeastOneCorrectAnswer') : Translation :: get('SelectACorrectAnswer');
            return array('change_answer_type' => $message);
        }
        return true;
    }

    public function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        
        $options = array();
        foreach ($values[AssessmentSelectQuestionOption :: PROPERTY_VALUE] as $option_id => $value)
        {
            $score = $values[AssessmentSelectQuestionOption :: PROPERTY_SCORE][$option_id];
            $feedback = $values[AssessmentSelectQuestionOption :: PROPERTY_FEEDBACK][$option_id];
            if ($_SESSION['select_answer_type'] == 'radio')
            {
                $correct = $values[AssessmentSelectQuestionOption :: PROPERTY_CORRECT] == $option_id;
            }
            else
            {
                $correct = $values[AssessmentSelectQuestionOption :: PROPERTY_CORRECT][$option_id];
            }
            $options[] = new AssessmentSelectQuestionOption($value, $correct, $score, $feedback);
        }
        
        $object->set_answer_type($_SESSION['select_answer_type']);
        $object->set_options($options);
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this multiple choice question
     */
    public function add_options()
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
            $switch_label = Translation :: get('SwitchToMultipleSelect');
        }
        elseif ($_SESSION['select_answer_type'] == 'checkbox')
        {
            $switch_label = Translation :: get('SwitchToSingleSelect');
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
            Translation :: get('AddSelectOption'), 
            array('id' => 'add_option'), 
            null, 
            'plus');
        $this->addGroup($buttons, 'question_buttons', null, '', false);
        
        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['toolbar'] = 'RepositoryQuestion';
        
        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox"></th>';
        $table_header[] = '<th style="width: 320px;">' . Translation :: get('Answer') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));
        
        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['select_skip_options']))
            {
                $group = array();
                
                if ($_SESSION['select_answer_type'] == 'checkbox')
                {
                    $group[] = & $this->createElement(
                        'checkbox', 
                        AssessmentSelectQuestionOption :: PROPERTY_CORRECT . '[' . $option_number . ']', 
                        Translation :: get('Correct'), 
                        '', 
                        array(
                            'class' => AssessmentSelectQuestionOption :: PROPERTY_VALUE, 
                            'id' => AssessmentSelectQuestionOption :: PROPERTY_CORRECT . '[' . $option_number . ']'));
                }
                else
                {
                    $group[] = & $this->createElement(
                        'radio', 
                        AssessmentSelectQuestionOption :: PROPERTY_CORRECT, 
                        Translation :: get('Correct'), 
                        '', 
                        $option_number, 
                        array(
                            'class' => AssessmentSelectQuestionOption :: PROPERTY_VALUE, 
                            'id' => AssessmentSelectQuestionOption :: PROPERTY_CORRECT . '[' . $option_number . ']'));
                }
                
                $group[] = & $this->createElement(
                    'text', 
                    AssessmentSelectQuestionOption :: PROPERTY_VALUE . '[' . $option_number . ']', 
                    Translation :: get('Answer'), 
                    array('style' => 'width: 300px;'));
                $group[] = & $this->create_html_editor(
                    AssessmentSelectQuestionOption :: PROPERTY_FEEDBACK . '[' . $option_number . ']', 
                    Translation :: get('Feedback'), 
                    $html_editor_options);
                $group[] = & $this->createElement(
                    'text', 
                    AssessmentSelectQuestionOption :: PROPERTY_SCORE . '[' . $option_number . ']', 
                    Translation :: get('Score'), 
                    'size="2"  class="input_numeric"');
                
                if ($number_of_options - count($_SESSION['select_skip_options']) > 2)
                {
                    $group[] = & $this->createElement(
                        'image', 
                        'remove[' . $option_number . ']', 
                        Theme :: getInstance()->getCommonImagePath('Action/Delete'), 
                        array('class' => 'remove_option', 'id' => 'remove_' . $option_number));
                }
                else
                {
                    $group[] = & $this->createElement(
                        'static', 
                        null, 
                        null, 
                        '<img class="remove_option" src="' .
                             Theme :: getInstance()->getCommonImagePath('Action/DeleteNa') . '" class="remove_option" />');
                }
                
                $this->addGroup(
                    $group, 
                    AssessmentSelectQuestionOption :: PROPERTY_VALUE . '_' . $option_number, 
                    null, 
                    '', 
                    false);
                
                $this->addGroupRule(
                    AssessmentSelectQuestionOption :: PROPERTY_VALUE . '_' . $option_number, 
                    array(
                        AssessmentSelectQuestionOption :: PROPERTY_SCORE . '[' . $option_number . ']' => array(
                            array(
                                Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES), 
                                'numeric'))));
                
                $renderer->setElementTemplate(
                    '<tr id="option_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') .
                         '">{element}</tr>', 
                        AssessmentSelectQuestionOption :: PROPERTY_VALUE . '_' . $option_number);
                $renderer->setGroupElementTemplate(
                    '<td>{element}</td>', 
                    AssessmentSelectQuestionOption :: PROPERTY_VALUE . '_' . $option_number);
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
