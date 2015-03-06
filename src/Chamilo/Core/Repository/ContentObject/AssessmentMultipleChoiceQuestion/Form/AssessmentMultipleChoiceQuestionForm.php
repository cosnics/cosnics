<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Form;

use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestionOption;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: assessment_multiple_choice_question_form.class.php $
 *
 * @package repository.lib.content_object.multiple_choice_question
 */
class AssessmentMultipleChoiceQuestionForm extends ContentObjectForm
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
            AssessmentMultipleChoiceQuestion :: PROPERTY_HINT,
            Translation :: get('Hint', array(), ClassnameUtilities :: getInstance()->getNamespaceFromObject($this)),
            false,
            $html_editor_options);
        $renderer->setElementTemplate(
            '{element}<div class="clear"></div>',
            AssessmentMultipleChoiceQuestion :: PROPERTY_HINT);
        $this->addElement('category');

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion',
                    true) . 'AssessmentMultipleChoiceQuestion.js'));

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
            AssessmentMultipleChoiceQuestion :: PROPERTY_HINT,
            Translation :: get('Hint', array(), ClassnameUtilities :: getInstance()->getNamespaceFromObject($this)),
            false,
            $html_editor_options);
        $renderer->setElementTemplate(
            '{element}<div class="clear"></div>',
            AssessmentMultipleChoiceQuestion :: PROPERTY_HINT);
        $this->addElement('category');

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion',
                    true) . 'AssessmentMultipleChoiceQuestion.js'));

        $this->add_example_box();
    }

    public function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            $defaults[AssessmentMultipleChoiceQuestion :: PROPERTY_HINT] = $object->get_hint();
            if ($object->get_number_of_options() != 0)
            {
                $options = $object->get_options();

                foreach ($options as $index => $option)
                {
                    $defaults[AssessmentMultipleChoiceQuestionOption :: PROPERTY_VALUE][$index] = $option->get_value();
                    $defaults[AssessmentMultipleChoiceQuestionOption :: PROPERTY_SCORE][$index] = $option->get_score();
                    $defaults[AssessmentMultipleChoiceQuestionOption :: PROPERTY_FEEDBACK][$index] = $option->get_feedback();
                    if ($object->get_answer_type() == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
                    {
                        $defaults[AssessmentMultipleChoiceQuestionOption :: PROPERTY_CORRECT][$index] = $option->is_correct();
                    }
                    elseif ($option->is_correct())
                    {
                        $defaults[AssessmentMultipleChoiceQuestionOption :: PROPERTY_CORRECT] = $index;
                    }
                }
            }
            else
            {
                $number_of_options = intval($_SESSION['mc_number_of_options']);

                for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults[AssessmentMultipleChoiceQuestionOption :: PROPERTY_SCORE][$option_number] = 0;
                }

                $defaults[AssessmentMultipleChoiceQuestionOption :: PROPERTY_CORRECT] = 0;
            }
        }
        parent :: setDefaults($defaults);
    }

    public function create_content_object()
    {
        $object = new AssessmentMultipleChoiceQuestion();
        $object->set_hint($this->exportValue(AssessmentMultipleChoiceQuestion :: PROPERTY_HINT));
        $this->set_content_object($object);
        $this->add_options_to_object();
        return parent :: create_content_object();
    }

    public function update_content_object()
    {
        $this->get_content_object()->set_hint($this->exportValue(AssessmentMultipleChoiceQuestion :: PROPERTY_HINT));
        $this->add_options_to_object();
        return parent :: update_content_object();
    }

    public function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();

        $object->set_hint($values[AssessmentMultipleChoiceQuestion :: PROPERTY_HINT]);

        $options = array();
        foreach ($values[AssessmentMultipleChoiceQuestionOption :: PROPERTY_VALUE] as $option_id => $value)
        {
            $score = $values[AssessmentMultipleChoiceQuestionOption :: PROPERTY_SCORE][$option_id];
            $feedback = $values[AssessmentMultipleChoiceQuestionOption :: PROPERTY_FEEDBACK][$option_id];
            if ($_SESSION['mc_answer_type'] == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO)
            {
                $correct = $values[AssessmentMultipleChoiceQuestionOption :: PROPERTY_CORRECT] == $option_id;
            }
            else
            {
                $correct = $values[AssessmentMultipleChoiceQuestionOption :: PROPERTY_CORRECT][$option_id];
            }
            $options[] = new AssessmentMultipleChoiceQuestionOption($value, $correct, $score, $feedback);
        }
        $object->set_answer_type($_SESSION['mc_answer_type']);
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
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
            unset($_SESSION['mc_answer_type']);
        }
        if (! isset($_SESSION['mc_number_of_options']))
        {
            $_SESSION['mc_number_of_options'] = 3;
        }
        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
        }
        if (! isset($_SESSION['mc_answer_type']))
        {
            $_SESSION['mc_answer_type'] = AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO;
        }
        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_skip_options'][] = $indexes[0];
        }
        if (isset($_POST['change_answer_type']))
        {
            $_SESSION['mc_answer_type'] = $_SESSION['mc_answer_type'] ==
                 AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO ? AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX : AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO;
        }
        $object = $this->get_content_object();
        if (! $this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            $_SESSION['mc_number_of_options'] = $object->get_number_of_options();
            $_SESSION['mc_answer_type'] = $object->get_answer_type();
        }
        $number_of_options = intval($_SESSION['mc_number_of_options']);

        if ($_SESSION['mc_answer_type'] == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO)
        {
            $switch_label = Translation :: get('SwitchToCheckboxes');
        }
        elseif ($_SESSION['mc_answer_type'] == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
        {
            $switch_label = Translation :: get('SwitchToRadioButtons');
        }

        $this->addElement('hidden', 'mc_answer_type', $_SESSION['mc_answer_type'], array('id' => 'mc_answer_type'));
        $this->addElement(
            'hidden',
            'mc_number_of_options',
            $_SESSION['mc_number_of_options'],
            array('id' => 'mc_number_of_options'));

        $buttons = array();
        $buttons[] = $this->createElement(
            'style_submit_button',
            'change_answer_type',
            $switch_label,
            array('class' => 'normal switch change_answer_type'));
        // Notice: The [] are added to this element name so we don't have to deal with the _x and _y suffixes added when
        // clicking an image button
        $buttons[] = $this->createElement(
            'style_button',
            'add[]',
            Translation :: get('AddMultipleChoiceOption'),
            array('class' => 'normal add add_option'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;
        $html_editor_options['toolbar_set'] = 'RepositoryQuestion';

        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox"></th>';
        $table_header[] = '<th>' . Translation :: get('Answer') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
        $table_header[] = '<th class="action"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));

        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();

                if ($_SESSION['mc_answer_type'] == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
                {
                    $group[] = & $this->createElement(
                        'checkbox',
                        AssessmentMultipleChoiceQuestionOption :: PROPERTY_CORRECT . '[' . $option_number . ']',
                        Translation :: get('Correct'),
                        '',
                        array(
                            'class' => AssessmentMultipleChoiceQuestionOption :: PROPERTY_VALUE,
                            'id' => AssessmentMultipleChoiceQuestionOption :: PROPERTY_CORRECT . '[' . $option_number .
                                 ']'));
                }
                else
                {
                    $group[] = & $this->createElement(
                        'radio',
                        AssessmentMultipleChoiceQuestionOption :: PROPERTY_CORRECT,
                        Translation :: get('Correct'),
                        '',
                        $option_number,
                        array(
                            'class' => AssessmentMultipleChoiceQuestionOption :: PROPERTY_VALUE,
                            'id' => AssessmentMultipleChoiceQuestionOption :: PROPERTY_CORRECT . '[' . $option_number .
                                 ']'));
                }

                $group[] = $this->create_html_editor(
                    AssessmentMultipleChoiceQuestionOption :: PROPERTY_VALUE . '[' . $option_number . ']',
                    Translation :: get('Answer'),
                    $html_editor_options);
                $group[] = $this->create_html_editor(
                    AssessmentMultipleChoiceQuestionOption :: PROPERTY_FEEDBACK . '[' . $option_number . ']',
                    Translation :: get('Feedback'),
                    $html_editor_options);
                $group[] = & $this->createElement(
                    'text',
                    AssessmentMultipleChoiceQuestionOption :: PROPERTY_SCORE . '[' . $option_number . ']',
                    Translation :: get('Score'),
                    'size="2"  class="input_numeric"');

                if ($number_of_options - count($_SESSION['mc_skip_options']) > 2)
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
                        '<img id="remove_' . $option_number . '" class="remove_option" src="' .
                             Theme :: getInstance()->getCommonImagePath('Action/DeleteNa') . '" class="remove_option" />');
                }

                $this->addGroup(
                    $group,
                    AssessmentMultipleChoiceQuestionOption :: PROPERTY_VALUE . '_' . $option_number,
                    null,
                    '',
                    false);

                $this->addGroupRule(
                    AssessmentMultipleChoiceQuestionOption :: PROPERTY_VALUE . '_' . $option_number,
                    array(
                        AssessmentMultipleChoiceQuestionOption :: PROPERTY_SCORE . '[' . $option_number . ']' => array(
                            array(
                                Translation :: get('ThisFieldShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
                                'numeric'))));

                $renderer->setElementTemplate(
                    '<tr id="option_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') .
                         '">{element}</tr>',
                        AssessmentMultipleChoiceQuestionOption :: PROPERTY_VALUE . '_' . $option_number);
                $renderer->setGroupElementTemplate(
                    '<td>{element}</td>',
                    AssessmentMultipleChoiceQuestionOption :: PROPERTY_VALUE . '_' . $option_number);
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
        if (! isset($fields[AssessmentMultipleChoiceQuestionOption :: PROPERTY_CORRECT]))
        {
            $message = $_SESSION['mc_answer_type'] == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX ? Translation :: get(
                'SelectAtLeastOneCorrectAnswer') : Translation :: get('SelectACorrectAnswer');
            return array('change_answer_type' => $message);
        }
        return true;
    }
}
