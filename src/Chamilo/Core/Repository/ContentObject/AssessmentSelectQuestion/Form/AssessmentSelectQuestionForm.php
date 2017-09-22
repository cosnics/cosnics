<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Form;

use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestionOption;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: assessment_select_question_form.class.php $
 *
 * @package repository.lib.content_object.select_question
 */
class AssessmentSelectQuestionForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent::build_creation_form($this->getDescriptionHtmlEditorOptions());
        $this->buildBasicQuestionForm();
    }

    protected function build_editing_form()
    {
        parent::build_editing_form($this->getDescriptionHtmlEditorOptions());
        $this->buildBasicQuestionForm();
    }

    protected function getDescriptionHtmlEditorOptions()
    {
        $htmlEditorOptions = array();
        $htmlEditorOptions['width'] = '100%';
        $htmlEditorOptions['height'] = '100';
        $htmlEditorOptions['collapse_toolbar'] = false;
        $htmlEditorOptions['show_tags'] = false;

        return $htmlEditorOptions;
    }

    protected function buildBasicQuestionForm()
    {
        $this->add_options();

        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion',
                    true) . 'AssessmentSelectQuestion.js'));
    }

    public function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            $defaults[AssessmentSelectQuestion::PROPERTY_HINT] = $object->get_hint();

            if ($object->get_number_of_options() != 0)
            {
                $options = $object->get_options();

                foreach ($options as $index => $option)
                {
                    $defaults[AssessmentSelectQuestionOption::PROPERTY_VALUE][$index] = $option->get_value() ? $option->get_value() : 0;
                    $defaults[AssessmentSelectQuestionOption::PROPERTY_SCORE][$index] = $option->get_score() ? $option->get_score() : 0;
                    $defaults[AssessmentSelectQuestionOption::PROPERTY_FEEDBACK][$index] = $option->get_feedback();

                    if ($object->get_answer_type() == AssessmentSelectQuestion::ANSWER_TYPE_CHECKBOX)
                    {
                        $defaults[AssessmentSelectQuestionOption::PROPERTY_CORRECT][$index] = $option->is_correct();
                    }
                    elseif ($option->is_correct())
                    {
                        $defaults[AssessmentSelectQuestionOption::PROPERTY_CORRECT] = $index;
                    }
                }
            }
            else
            {
                $number_of_options = (int) Session::retrieve('select_number_of_options');

                for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
                {
                    $defaults[AssessmentSelectQuestionOption::PROPERTY_SCORE][$option_number] = 0;
                }

                $defaults[AssessmentSelectQuestionOption::PROPERTY_CORRECT] = 0;
            }
        }

        parent::setDefaults($defaults);
    }

    public function create_content_object()
    {
        $this->set_content_object(new AssessmentSelectQuestion());
        $this->processSubmittedData();

        return parent::create_content_object();
    }

    public function update_content_object()
    {
        $this->processSubmittedData();
        return parent::update_content_object();
    }

    public function processSubmittedData()
    {
        $this->get_content_object()->set_hint($this->exportValue(AssessmentSelectQuestion::PROPERTY_HINT));
        $this->add_options_to_object();
    }

    public function validate()
    {
        $extraOptionRequested = Request::post('add');
        $removedOptions = Request::post('remove');
        $answerTypeChanged = Request::post('change_answer_type');

        if (isset($extraOptionRequested) || isset($removedOptions) || isset($answerTypeChanged))
        {
            return false;
        }

        return parent::validate();
    }

    public function validate_selected_answers($fields)
    {
        $answerType = Session::retrieve('select_answer_type');

        if (! isset($fields[AssessmentSelectQuestionOption::PROPERTY_CORRECT]))
        {
            $message = ($answerType == AssessmentSelectQuestionOption::ANSWER_TYPE_CHECKBOX ? Translation::get(
                'SelectAtLeastOneCorrectAnswer') : Translation::get('SelectACorrectAnswer'));
            return array('change_answer_type' => $message);
        }

        return true;
    }

    public function add_options_to_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $answerType = Session::retrieve('select_answer_type');

        $options = array();

        foreach ($values[AssessmentSelectQuestionOption::PROPERTY_VALUE] as $option_id => $value)
        {
            $score = $values[AssessmentSelectQuestionOption::PROPERTY_SCORE][$option_id];
            $feedback = $values[AssessmentSelectQuestionOption::PROPERTY_FEEDBACK][$option_id];

            if ($answerType == AssessmentSelectQuestion::ANSWER_TYPE_RADIO)
            {
                $correct = $values[AssessmentSelectQuestionOption::PROPERTY_CORRECT] == $option_id;
            }
            else
            {
                $correct = $values[AssessmentSelectQuestionOption::PROPERTY_CORRECT][$option_id];
            }

            $options[] = new AssessmentSelectQuestionOption($value, $correct, $score, $feedback);
        }

        $object->set_answer_type($answerType);
        $object->set_options($options);
    }

    public function setOptionsSessionValues()
    {
        if (! $this->isSubmitted())
        {
            Session::unregister('select_number_of_options');
            Session::unregister('select_skip_options');
            Session::unregister('select_answer_type');
        }

        Session::registerIfNotSet('select_number_of_options', 3);
        Session::registerIfNotSet('select_skip_options', array());
        Session::registerIfNotSet('select_answer_type', AssessmentSelectQuestion::ANSWER_TYPE_RADIO);

        $extraOptionRequested = Request::post('add');
        $removedOptions = Request::post('remove');
        $answerTypeChanged = Request::post('change_answer_type');

        if (isset($extraOptionRequested))
        {
            Session::register('select_number_of_options', (Session::retrieve('select_number_of_options') + 1));
        }

        if (isset($removedOptions))
        {
            $indexes = array_keys($removedOptions);
            $skippedOptions = Session::retrieve('select_skip_options');
            $skippedOptions[] = $indexes[0];

            Session::register('select_skip_options', $skippedOptions);
        }

        if (isset($answerTypeChanged))
        {
            $currentAnswerType = Session::retrieve('select_answer_type');

            if ($currentAnswerType == AssessmentSelectQuestion::ANSWER_TYPE_RADIO)
            {
                $newAnswerType = AssessmentSelectQuestion::ANSWER_TYPE_CHECKBOX;
            }
            else
            {
                $newAnswerType = AssessmentSelectQuestion::ANSWER_TYPE_RADIO;
            }

            Session::register('select_answer_type', $newAnswerType);
        }

        $object = $this->get_content_object();

        if (! $this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            Session::register('select_number_of_options', $object->get_number_of_options());
            Session::register('select_answer_type', $object->get_answer_type());
        }
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this multiple choice question
     */
    public function add_options()
    {
        $renderer = $this->defaultRenderer();
        $this->setOptionsSessionValues();

        $number_of_options = (int) Session::retrieve('select_number_of_options');
        $skippedOptions = Session::retrieve('select_skip_options');
        $answerType = Session::retrieve('select_answer_type');

        $this->addElement('category', Translation::get('Options'));

        $this->addElement('hidden', 'select_answer_type', $answerType, array('id' => 'select_answer_type'));
        $this->addElement(
            'hidden',
            'select_number_of_options',
            $number_of_options,
            array('id' => 'select_number_of_options'));

        $htmlEditorOptions = array();
        $htmlEditorOptions['width'] = '100%';
        $htmlEditorOptions['height'] = '65';
        $htmlEditorOptions['collapse_toolbar'] = true;

        $this->addElement('html', '<table class="table table-assessment-question-form"><tbody>');

        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $skippedOptions))
            {
                $this->addElement('html', '<tr data-option-id="' . $option_number . '">');

                // Checkbox or radio button
                $attributes = array('class' => 'option-value', 'data-option-id' => $option_number);

                if ($answerType == AssessmentSelectQuestion::ANSWER_TYPE_CHECKBOX)
                {
                    $selectionName = AssessmentSelectQuestionOption::PROPERTY_CORRECT . '[' . $option_number . ']';
                    $this->addElement('checkbox', $selectionName, Translation::get('Correct'), '', $attributes);
                }
                else
                {
                    $selectionName = AssessmentSelectQuestionOption::PROPERTY_CORRECT;
                    $this->addElement(
                        'radio',
                        $selectionName,
                        Translation::get('Correct'),
                        '',
                        $option_number,
                        $attributes);
                }

                $renderer->setElementTemplate(
                    '<td class="table-cell-selection cell-stat-x2">{element}</td>',
                    $selectionName);

                $this->addElement('html', '<td>');

                // Answer
                $this->addElement(
                    'text',
                    AssessmentSelectQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']',
                    Translation::get('Answer'),
                    array('class' => 'form-control'));

                $renderer->setElementTemplate(
                    '<div class="option-answer-field" data-element="' . AssessmentSelectQuestionOption::PROPERTY_VALUE .
                         '[' . $option_number . ']' . '">{element}</div>',
                        AssessmentSelectQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']');

                // Feedback
                $this->add_html_editor(
                    AssessmentSelectQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']',
                    Translation::get('Feedback'),
                    false,
                    $htmlEditorOptions);

                $renderer->setElementTemplate(
                    '<div class="option-feedback-field form-assessment-extra-container" data-element="' .
                         AssessmentSelectQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']' .
                         '"><label>{label}</label>{element}</div>',
                        AssessmentSelectQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']');

                // Score
                $this->addElement(
                    'text',
                    AssessmentSelectQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']',
                    Translation::get('Score'),
                    'size="2"  class="input_numeric form-control"');

                $renderer->setElementTemplate(
                    '<div class="option-score-field form-assessment-extra-container form-inline" data-element="' .
                         AssessmentSelectQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']' .
                         '"><label>{label}:</label> {element}</div>',
                        AssessmentSelectQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']');

                $this->addElement('html', '</td>');

                $this->addElement('html', '<td class="table-cell-action cell-stat text-right">');

                $actionButtons = array();

                if ($number_of_options - count($skippedOptions) > 2)
                {
                    $removeClass = 'text-danger';
                }
                else
                {
                    $removeClass = 'text-muted';
                }

                $actionButtons[] = '<span data-option-id="' . $option_number .
                     '" class="option-action option-feedback fa fa-comment text-primary"></span>';
                $actionButtons[] = '<span data-option-id="' . $option_number .
                     '" class="option-action option-score fa fa-percent text-primary"></span>';
                $actionButtons[] = '<span data-option-id="' . $option_number .
                     '" class="option-action option-remove fa fa-trash ' . $removeClass . '"></span>';

                $this->addElement('html', implode('&nbsp;&nbsp;', $actionButtons));

                $this->addElement('html', '</td>');
                $this->addElement('html', '</tr>');
            }
        }

        $this->addElement('html', '</tbody></table>');

        $this->addOptionsButtons();
    }

    protected function addOptionsButtons()
    {
        $answerType = Session::retrieve('select_answer_type');

        if ($answerType == AssessmentSelectQuestion::ANSWER_TYPE_RADIO)
        {
            $switchLabel = Translation::get('SwitchToMultipleSelect');
        }
        else
        {
            $switchLabel = Translation::get('SwitchToSingleSelect');
        }

        $buttonToolBar = new ButtonToolBar();

        $buttonToolBar->addItem(
            new Button(
                Translation::get('AddSelectOption'),
                new FontAwesomeGlyph('plus'),
                ' ',
                Button::DISPLAY_ICON_AND_LABEL,
                false,
                'btn-primary add-option'));
        $buttonToolBar->addItem(
            new Button(
                $switchLabel,
                new FontAwesomeGlyph('retweet'),
                ' ',
                Button::DISPLAY_ICON_AND_LABEL,
                false,
                'change-answer-type'));

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        $this->addElement('html', $buttonToolBarRenderer->render());
    }

    public function prepareTabs()
    {
        $this->addDefaultTab();
        $this->addHintTab();
        $this->addInstructionsTab();
        $this->addMetadataTabs();
    }

    public function addHintTab()
    {
        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                'add-hint',
                Translation::get('AddHint'),
                new FontAwesomeGlyph('magic', array('ident-sm')),
                'buildHintForm'));
    }

    public function buildHintForm()
    {
        $htmlEditorOptions = array();
        $htmlEditorOptions['width'] = '100%';
        $htmlEditorOptions['height'] = '100';
        $htmlEditorOptions['collapse_toolbar'] = true;
        $htmlEditorOptions['show_tags'] = false;

        $this->add_html_editor(
            AssessmentSelectQuestion::PROPERTY_HINT,
            Translation::get('Hint', array(), ClassnameUtilities::getInstance()->getNamespaceFromObject($this)),
            false,
            $htmlEditorOptions);
    }
}
