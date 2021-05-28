<?php
namespace Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Form;

use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestion;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\DynamicFormTab;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.question_types.fill_in_blanks_question
 */
class FillInBlanksQuestionForm extends ContentObjectForm
{
    // Tabs
    const DEFAULT_FIELD_OPTION_SIZE = 15;

    const DEFAULT_FIELD_OPTION_VARIATION = 0;

    // Fields

    const DEFAULT_UNIFORM_TYPE = self::UNIFORM_FIXED_ANSWER;

    const FIELD_OPTION_SIZE = 'field_option_size';

    const FIELD_OPTION_VARIATION = 'field_option_variation';

    const TAB_BLANKS = 'Blanks';

    const TAB_EXAMPLE = 'Example';

    const UNIFORM_FIXED_ANSWER = 1;

    const UNIFORM_INPUT_TYPE = 'uniform_input_type';

    const UNIFORM_LONGEST_ANSWER = 0;

    const UNIFORM_UNLIMITED_ANSWER = - 1;

    /**
     * Adds the form-fields to the form to provide the possible options for this multiple choice question
     */
    private function add_options($question)
    {
        $answers = $question->get_answers();
        $default_score = $question->get_default_positive_score();
        if (is_null($default_score))
        {
            $default_score = FillInBlanksQuestion::DEFAULT_POSITIVE_SCORE;
        }

        $style = (count($answers) == 0) ? 'style="display: none;"' : '';

        $html = [];
        $html[] = '<div id="answers_table" class="' . $style . '>';
        $html[] = '<div class="label">';
        $html[] = Translation::get('Answers');
        $html[] = '</div>';
        $html[] = '<div class="formw">';
        $html[] = '<div class="element">';
        $html[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th>' . Translation::get('Question') . '</th>';
        $html[] = '<th>' . Translation::get('Answer') . '</th>';
        $html[] = '<th>' . Translation::get('Feedback') . '</th>';
        $html[] = '<th>' . Translation::get('Hint') . '</th>';
        $html[] = '<th class="cell-stat-x2">' . Translation::get('NonDefaultScore') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        foreach ($answers as $answer)
        {
            // filter out default scores
            $answer_score = $answer->get_weight();
            if ($answer_score == $default_score)
            {
                $answer_score = '';
            }

            // build table
            $html[] = '<tr>';
            $html[] = '<td>' . ($answer->get_position() + 1) . '</td>';
            $html[] = '<td>' . str_replace(PHP_EOL, '<br/>', $answer->get_value()) . '</td>';
            $html[] = '<td>' . str_replace(PHP_EOL, '<br/>', $answer->get_comment()) . ' </td>';
            $html[] = '<td>' . str_replace(PHP_EOL, '<br/>', $answer->get_hint()) . ' </td>';
            $html[] = '<td>' . $answer_score . '</td>';
            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '</table>';
        $html[] = '</div>';
        $html[] = '<div class="form_feedback"></div></div>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';

        $this->addElement('html', implode(PHP_EOL, $html));
    }

    public function buildBlanksForm()
    {
        // ANSWERS
        $this->addElement('category', Translation::get('Excercise'));

        $this->addElement(
            'textarea', FillInBlanksQuestion::PROPERTY_ANSWER_TEXT, Translation::get('QuestionText'),
            'rows="10" class="form-control"'
        );
        $this->addRule(
            FillInBlanksQuestion::PROPERTY_ANSWER_TEXT,
            Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required'
        );
        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(
                'Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion', true
            ) . 'FillInTheBlanks.js'
        )
        );
        $this->add_options($this->get_content_object());

        // ANSWER TYPE
        $this->addElement('category', Translation::get('AnswerType'));
        $type_options = [];
        $type_options[] = $this->createElement(
            'radio', FillInBlanksQuestion::PROPERTY_QUESTION_TYPE, null, Translation::get('AutoTextField'),
            FillInBlanksQuestion::TYPE_SIZED_TEXT,
            array('class' => 'type_' . FillInBlanksQuestion::TYPE_SIZED_TEXT . '_option_selector')
        );
        $type_options[] = $this->createElement(
            'radio', FillInBlanksQuestion::PROPERTY_QUESTION_TYPE, null, Translation::get('UniformTextField'),
            FillInBlanksQuestion::TYPE_UNIFORM_TEXT,
            array('class' => 'type_' . FillInBlanksQuestion::TYPE_UNIFORM_TEXT . '_option_selector')
        );
        $type_options[] = $this->createElement(
            'radio', FillInBlanksQuestion::PROPERTY_QUESTION_TYPE, null, Translation::get('SelectBox'),
            FillInBlanksQuestion::TYPE_SELECT,
            array('class' => 'type_' . FillInBlanksQuestion::TYPE_SELECT . '_option_selector')
        );
        $this->addElement('group', null, Translation::get('UseSelectBox'), $type_options, '', false);

        // show inline
        $this->addElement(
            'checkbox', FillInBlanksQuestion::PROPERTY_SHOW_INLINE, Translation::get('ShowInputFieldsInline')
        );

        // case sensitive
        $this->addElement('checkbox', FillInBlanksQuestion::PROPERTY_CASE_SENSITIVE, Translation::get('CaseSensitive'));

        // ANSWER OPTIONS
        $this->addElement('category', Translation::get('AnswerOptions'));

        // default scores
        $this->add_textfield(
            FillInBlanksQuestion::PROPERTY_DEFAULT_POSITIVE_SCORE, Translation::get('DefaultPositiveScore'), false,
            array("size" => "5")
        );

        $this->add_textfield(
            FillInBlanksQuestion::PROPERTY_DEFAULT_NEGATIVE_SCORE, Translation::get('DefaultNegativeScore'), false,
            array("size" => "5")
        );

        $this->addRule(
            array(
                FillInBlanksQuestion::PROPERTY_DEFAULT_POSITIVE_SCORE,
                FillInBlanksQuestion::PROPERTY_DEFAULT_NEGATIVE_SCORE
            ), Translation::get('DefaultPositiveScoreBiggerDefaultNegativeScore'), 'compare', '>'
        );
        $this->addRule(
            array(FillInBlanksQuestion::PROPERTY_DEFAULT_POSITIVE_SCORE),
            Translation::get('DefaultPositiveScoreBigger'), 'number_compare', '>', 0
        );

        // auto sized text field
        $this->addElement('html', '<div class="type_' . FillInBlanksQuestion::TYPE_SIZED_TEXT . '_options_box">');
        $this->add_textfield(self::FIELD_OPTION_VARIATION, Translation::get('Variation'), false, array("size" => "5"));
        $this->addElement('html', '</div>');

        // uniform sized text field
        $this->addElement('html', '<div class="type_' . FillInBlanksQuestion::TYPE_UNIFORM_TEXT . '_options_box">');
        $field_size_options = [];
        // $field_size_options[] = $this->createElement('radio', self::UNIFORM_INPUT_TYPE, null, Translation ::
        // get('Unlimited') . '<br />', self::UNIFORM_UNLIMITED_ANSWER);
        $field_size_options[] = $this->createElement(
            'radio', self::UNIFORM_INPUT_TYPE, null, Translation::get('LongestAnswer') . '<br />',
            self::UNIFORM_LONGEST_ANSWER
        );
        $field_size_options[] = $this->createElement(
            'radio', self::UNIFORM_INPUT_TYPE, null, null, self::UNIFORM_FIXED_ANSWER
        );
        $field_size_options[] = $this->createElement('text', self::FIELD_OPTION_SIZE, null, null, null);
        $this->addGroup($field_size_options, null, Translation::get('FieldSize'), '');
        $this->addElement('html', '</div>');
    }

    public function create_content_object()
    {
        $values = $this->exportValues();
        $object = new FillInBlanksQuestion();
        $this->set_content_object($object);
        $object->set_answer_text($values[FillInBlanksQuestion::PROPERTY_ANSWER_TEXT]);
        $object->set_case_sensitive($values[FillInBlanksQuestion::PROPERTY_CASE_SENSITIVE]);
        $object->set_default_positive_score($values[FillInBlanksQuestion::PROPERTY_DEFAULT_POSITIVE_SCORE]);
        $object->set_default_negative_score($values[FillInBlanksQuestion::PROPERTY_DEFAULT_NEGATIVE_SCORE]);
        $object->set_show_inline($values[FillInBlanksQuestion::PROPERTY_SHOW_INLINE]);
        $this->set_type_options($object, $values);

        return parent::create_content_object();
    }

    /**
     * Prepare all the different tabs
     */
    public function prepareTabs()
    {
        $this->addDefaultTab();

        $this->getTabsGenerator()->add_tab(
            new DynamicFormTab(
                self::TAB_BLANKS, Translation::get(self::TAB_BLANKS), new FontAwesomeGlyph('edit', array('fa-sm')),
                'buildBlanksForm'
            )
        );

        $this->addInstructionsTab();

        $this->addMetadataTabs();
    }

    public function setDefaults($defaults = [], $filter = null)
    {
        if (!$this->isSubmitted())
        {
            $object = $this->get_content_object();

            if ($object->get_answer_text())
            {
                // $options = $object->get_answers();
                $defaults[FillInBlanksQuestion::PROPERTY_ANSWER_TEXT] = $object->get_answer_text();
                $defaults[FillInBlanksQuestion::PROPERTY_CASE_SENSITIVE] = $object->get_case_sensitive();
                $defaults[FillInBlanksQuestion::PROPERTY_QUESTION_TYPE] = $object->get_question_type();
                $defaults[FillInBlanksQuestion::PROPERTY_DEFAULT_POSITIVE_SCORE] =
                    $object->get_default_positive_score();
                $defaults[FillInBlanksQuestion::PROPERTY_DEFAULT_NEGATIVE_SCORE] =
                    $object->get_default_negative_score();
                $defaults[FillInBlanksQuestion::PROPERTY_SHOW_INLINE] = $object->get_show_inline();

                $option = $object->get_field_option();
                if (isset($option)) // set defaults if we don't have option value, otherwise use value
                {
                    $defaults[self::FIELD_OPTION_VARIATION] = $option;
                    $defaults[self::FIELD_OPTION_SIZE] = $option;

                    switch ($option) // filter out -1 and 0, fixed in any other case
                    {
                        case self::UNIFORM_UNLIMITED_ANSWER :
                            $defaults[self::UNIFORM_INPUT_TYPE] = self::UNIFORM_UNLIMITED_ANSWER;
                            break;
                        case self::UNIFORM_LONGEST_ANSWER :
                            $defaults[self::UNIFORM_INPUT_TYPE] = self::UNIFORM_LONGEST_ANSWER;
                            break;
                        default :
                            $defaults[self::UNIFORM_INPUT_TYPE] = self::UNIFORM_FIXED_ANSWER;
                            break;
                    }
                }
                else
                {
                    $defaults[self::FIELD_OPTION_VARIATION] = self::DEFAULT_FIELD_OPTION_VARIATION;
                    $defaults[self::FIELD_OPTION_SIZE] = self::DEFAULT_FIELD_OPTION_SIZE;
                    $defaults[self::UNIFORM_INPUT_TYPE] = self::DEFAULT_UNIFORM_TYPE;
                }
            }
            else
            {
                $defaults[FillInBlanksQuestion::PROPERTY_ANSWER_TEXT] = FillInBlanksQuestion::DEFAULT_INPUT_ANSWER_TEXT;
                $defaults[FillInBlanksQuestion::PROPERTY_CASE_SENSITIVE] = FillInBlanksQuestion::DEFAULT_CASE_SENSITIVE;
                $defaults[FillInBlanksQuestion::PROPERTY_QUESTION_TYPE] = FillInBlanksQuestion::DEFAULT_INPUT_TYPE;
                $defaults[FillInBlanksQuestion::PROPERTY_DEFAULT_POSITIVE_SCORE] =
                    FillInBlanksQuestion::DEFAULT_POSITIVE_SCORE;
                $defaults[FillInBlanksQuestion::PROPERTY_DEFAULT_NEGATIVE_SCORE] =
                    FillInBlanksQuestion::DEFAULT_NEGATIVE_SCORE;
                $defaults[FillInBlanksQuestion::PROPERTY_SHOW_INLINE] = FillInBlanksQuestion::DEFAULT_SHOW_INLINE;
                $defaults[self::UNIFORM_INPUT_TYPE] = self::DEFAULT_UNIFORM_TYPE;
                $defaults[self::FIELD_OPTION_VARIATION] = self::DEFAULT_FIELD_OPTION_VARIATION;
                $defaults[self::FIELD_OPTION_SIZE] = self::DEFAULT_FIELD_OPTION_SIZE;
            }

            parent::setDefaults($defaults);
        }
    }

    public function set_type_options($object, $values)
    {
        // set type
        $type = $values[FillInBlanksQuestion::PROPERTY_QUESTION_TYPE];
        $object->set_question_type($type);

        // set type specific options
        switch ($type)
        {
            case FillInBlanksQuestion::TYPE_SIZED_TEXT :
                $object->set_field_option($values[self::FIELD_OPTION_VARIATION]);
                break;
            case FillInBlanksQuestion::TYPE_UNIFORM_TEXT :
                if ($values[self::UNIFORM_INPUT_TYPE] == self::UNIFORM_FIXED_ANSWER)
                {
                    $object->set_field_option($values[self::FIELD_OPTION_SIZE]);
                }
                else
                {
                    $object->set_field_option($values[self::UNIFORM_INPUT_TYPE]);
                }
                break;
            default :
                $object->set_field_option(null);
        }
    }

    public function update_content_object()
    {
        $values = $this->exportValues();
        $object = $this->get_content_object();
        $object->set_answer_text($values[FillInBlanksQuestion::PROPERTY_ANSWER_TEXT]);
        $object->set_case_sensitive($values[FillInBlanksQuestion::PROPERTY_CASE_SENSITIVE]);
        $object->set_default_positive_score($values[FillInBlanksQuestion::PROPERTY_DEFAULT_POSITIVE_SCORE]);
        $object->set_default_negative_score($values[FillInBlanksQuestion::PROPERTY_DEFAULT_NEGATIVE_SCORE]);
        $object->set_show_inline($values[FillInBlanksQuestion::PROPERTY_SHOW_INLINE]);
        $this->set_type_options($object, $values);

        return parent::update_content_object();
    }

    public function validate()
    {
        if (isset($_POST['add']))
        {
            return false;
        }

        return parent::validate();
    }
}
