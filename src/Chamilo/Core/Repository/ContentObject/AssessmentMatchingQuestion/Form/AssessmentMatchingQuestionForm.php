<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Form;

use Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass\AssessmentMatchingQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass\AssessmentMatchingQuestionOption;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.content_object.matching_question
 */
class AssessmentMatchingQuestionForm extends ContentObjectForm
{
    const PROPERTY_ADD_MATCH = 'add_match';

    const PROPERTY_ADD_OPTION = 'add_option';

    const PROPERTY_DEFAULTS_MATCH = 'match';

    const PROPERTY_DEFAULTS_MATCHES_TO = 'matches_to';

    const PROPERTY_MQ_NUMBER_OF_MATCHES = 'mq_number_of_matches';

    const PROPERTY_MQ_NUMBER_OF_OPTIONS = 'mq_number_of_options';

    const PROPERTY_MQ_SKIP_MATCHES = 'mq_skip_matches';

    const PROPERTY_MQ_SKIP_OPTIONS = 'mq_skip_options';

    const PROPERTY_REMOVE_MATCH = 'remove_match';

    const PROPERTY_REMOVE_OPTION = 'remove_option';

    /**
     * Adds the answer to the current learning object.
     * This function adds the list of possible options and matches and
     * the relation between the options and the matches to the question.
     */
    public function add_answer()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();

        $options = array();
        $matches = array();

        // Get an array with a mapping from the match-id to its index in the $values['match'] array
        $matches_indexes = array_flip(array_keys($values[self::PROPERTY_DEFAULTS_MATCH]));
        foreach ($values[AssessmentMatchingQuestionOption::PROPERTY_VALUE] as $option_id => $value)
        {
            // Create the option with it corresponding match
            $options[] = new AssessmentMatchingQuestionOption(
                $value, $matches_indexes[$values[self::PROPERTY_DEFAULTS_MATCHES_TO][$option_id]],
                $values[AssessmentMatchingQuestionOption::PROPERTY_SCORE][$option_id],
                $values[AssessmentMatchingQuestionOption::PROPERTY_FEEDBACK][$option_id]
            );
        }

        foreach ($values[self::PROPERTY_DEFAULTS_MATCH] as $match)
        {
            $matches[] = $match;
        }
        $object->set_options($options);
        $object->set_matches($matches);
    }

    /**
     * Adds the form-fields to the form to provide the possible matches for this matching question
     */
    public function add_matches()
    {
        $number_of_matches = intval($_SESSION[self::PROPERTY_MQ_NUMBER_OF_MATCHES]);
        $this->addElement('category', Translation::get('Matches'));

        $this->addElement(
            'hidden', self::PROPERTY_MQ_NUMBER_OF_MATCHES, $_SESSION[self::PROPERTY_MQ_NUMBER_OF_MATCHES],
            array('id' => self::PROPERTY_MQ_NUMBER_OF_MATCHES)
        );

        $buttons = array();
        $buttons[] = $this->createElement(
            'style_button', 'add_match[]', Translation::get('AddMatch'), array('id' => self::PROPERTY_ADD_MATCH), null,
            new FontAwesomeGlyph('plus')
        );
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer = $this->defaultRenderer();

        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data matches">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="cell-stat-x3"></th>';
        $table_header[] = '<th>' . Translation::get('Answer') . '</th>';
        $table_header[] = '<th class="cell-stat-x2"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;

        $label = 'A';

        $defaults = array();

        for ($match_number = 0; $match_number < $number_of_matches; $match_number ++)
        {
            $group = array();

            if (!in_array($match_number, $_SESSION[self::PROPERTY_MQ_SKIP_MATCHES]))
            {
                $defaults['match_label'][$match_number] = $label ++;
                $element = $this->createElement(
                    'text', 'match_label[' . $match_number . ']', Translation::get('Match'), 'style="width: 90%;" '
                );
                $element->freeze();
                $group[] = $element;
                $group[] = $this->create_html_editor(
                    'match[' . $match_number . ']', Translation::get('Match'), $html_editor_options
                );

                if ($number_of_matches - count($_SESSION[self::PROPERTY_MQ_SKIP_MATCHES]) > 2)
                {
                    $group[] = $this->createElement(
                        'style_button', 'remove_match[' . $match_number . ']', null,
                        array('class' => self::PROPERTY_REMOVE_MATCH, 'id' => 'remove_match_' . $match_number), null,
                        new FontAwesomeGlyph('times', array(), null, 'fas')
                    );
                }
                else
                {
                    $glyph = new FontAwesomeGlyph('times', array('text-muted', 'remove_match'));
                    $group[] = &$this->createElement('static', null, null, $glyph->render());
                }

                $this->addGroup($group, 'match_' . $match_number, null, '', false);

                $renderer->setElementTemplate(
                    '<tr id="match_' . $match_number . '" class="' .
                    ($match_number - 1 % 2 == 0 ? 'row_odd' : 'row_even') . '">{element}</tr>', 'match_' . $match_number
                );
                $renderer->setGroupElementTemplate('<td>{element}</td>', 'match_' . $match_number);

                // $this->addGroupRule('match_' . $match_number, array(
                // 'match[' . $match_number . ']' => array(
                // array(
                // Translation::get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES),
                // 'required'))));
            }
        }

        $this->setConstants($defaults);

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode(PHP_EOL, $table_footer));

        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer->setElementTemplate(
            '<div style="margin: 10px 0px 10px 0px;">{element}<div class="clearfix"></div></div>', 'question_buttons'
        );
        $renderer->setGroupElementTemplate(
            '<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons'
        );
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this matching question
     *
     * @todo Add rules to require options and matches
     */
    public function add_options()
    {
        $number_of_options = intval($_SESSION[self::PROPERTY_MQ_NUMBER_OF_OPTIONS]);
        $matches = array();
        $match_label = 'A';

        for ($match_number = 0; $match_number < $_SESSION[self::PROPERTY_MQ_NUMBER_OF_MATCHES]; $match_number ++)
        {
            if (!in_array($match_number, $_SESSION[self::PROPERTY_MQ_SKIP_MATCHES]))
            {
                $matches[$match_number] = $match_label ++;
            }
        }

        $this->addElement('category', Translation::get('Options'));
        $this->addElement(
            'hidden', self::PROPERTY_MQ_NUMBER_OF_OPTIONS, $_SESSION[self::PROPERTY_MQ_NUMBER_OF_OPTIONS],
            array('id' => self::PROPERTY_MQ_NUMBER_OF_OPTIONS)
        );

        $buttons = array();
        $buttons[] = $this->createElement(
            'style_button', 'add_option[]', Translation::get('AddMatchingQuestionOption'),
            array('id' => self::PROPERTY_ADD_OPTION), null, new FontAwesomeGlyph('plus')
        );
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer = $this->defaultRenderer();

        $table_header = array();
        $table_header[] = '<table class="table table-striped table-bordered table-hover table-data options">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="cell-stat-x3"></th>';
        $table_header[] = '<th>' . Translation::get('Answer') . '</th>';
        $table_header[] = '<th class="code">' . Translation::get('Matches') . '</th>';
        $table_header[] = '<th>' . Translation::get('Feedback') . '</th>';
        $table_header[] = '<th class="cell-stat-x2">' . Translation::get('Score') . '</th>';
        $table_header[] = '<th class="cell-stat-x2"></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode(PHP_EOL, $table_header));

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['show_tags'] = false;

        $visual_number = 0;

        for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {

            $group = array();
            if (!in_array($option_number, $_SESSION[self::PROPERTY_MQ_SKIP_OPTIONS]))
            {
                $visual_number ++;
                $group[] = $this->createElement('static', null, null, $visual_number);
                $group[] = $this->create_html_editor(
                    AssessmentMatchingQuestionOption::PROPERTY_VALUE . '[' . $option_number . ']',
                    Translation::get('Answer'), $html_editor_options
                );
                $group[] = $this->createElement(
                    'select', 'matches_to[' . $option_number . ']', Translation::get('Matches'), $matches
                );
                $group[] = $this->create_html_editor(
                    AssessmentMatchingQuestionOption::PROPERTY_FEEDBACK . '[' . $option_number . ']',
                    Translation::get('Comment'), $html_editor_options
                );
                $group[] = $this->createElement(
                    'text', AssessmentMatchingQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']',
                    Translation::get('Weight'), 'size="2"  class="input_numeric"'
                );

                if ($number_of_options - count($_SESSION[self::PROPERTY_MQ_SKIP_OPTIONS]) > 2)
                {
                    $group[] = $this->createElement(
                        'style_button', 'remove_option[' . $option_number . ']', null,
                        array('class' => self::PROPERTY_REMOVE_OPTION, 'id' => 'remove_option_' . $option_number), null,
                        new FontAwesomeGlyph('times', array(), null, 'fas')
                    );
                }
                else
                {
                    $glyph = new FontAwesomeGlyph('times', array('text-muted', 'remove_option'));
                    $group[] = &$this->createElement('static', null, null, $glyph->render());
                }

                $this->addGroup(
                    $group, AssessmentMatchingQuestionOption::PROPERTY_VALUE . '_' . $option_number, null, '', false
                );

                $this->addGroupRule(
                    AssessmentMatchingQuestionOption::PROPERTY_VALUE . '_' . $option_number, array(
                        AssessmentMatchingQuestionOption::PROPERTY_SCORE . '[' . $option_number . ']' => array(
                            array(
                                Translation::get('ThisFieldShouldBeNumeric', null, Utilities::COMMON_LIBRARIES),
                                'numeric'
                            )
                        )
                    )
                );

                $renderer->setElementTemplate(
                    '<tr id="option_' . $option_number . '" class="' .
                    ($visual_number % 2 == 0 ? 'row_odd' : 'row_even') . '">{element}</tr>',
                    AssessmentMatchingQuestionOption::PROPERTY_VALUE . '_' . $option_number
                );
                $renderer->setGroupElementTemplate(
                    '<td>{element}</td>', AssessmentMatchingQuestionOption::PROPERTY_VALUE . '_' . $option_number
                );
            }
        }
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode(PHP_EOL, $table_footer));

        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer->setElementTemplate(
            '<div style="margin: 10px 0px 10px 0px;">{element}<div class="clearfix"></div></div>', 'question_buttons'
        );
        $renderer->setGroupElementTemplate(
            '<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons'
        );
    }

    protected function build_creation_form()
    {
        parent::build_creation_form();
        $this->build_options_and_matches();
        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(
                'Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion', true
            ) . 'AssessmentMatchingQuestion.js'
        )
        );
    }

    protected function build_editing_form()
    {
        parent::build_editing_form();
        $this->build_options_and_matches();
        $this->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(
                'Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion', true
            ) . 'AssessmentMatchingQuestion.js'
        )
        );
    }

    /**
     * Adds the options and matches to the form
     */
    private function build_options_and_matches()
    {
        $select_options = array();
        $select_options[AssessmentMatchingQuestion::DISPLAY_LIST] = Translation::get('DisplayList');
        $select_options[AssessmentMatchingQuestion::DISPLAY_SELECT] = Translation::get('DisplaySelect');

        $this->addElement('category', Translation::get('Properties'));
        $this->addElement(
            'select', AssessmentMatchingQuestion::PROPERTY_DISPLAY, Translation::get('Display'), $select_options
        );

        $this->update_number_of_options_and_matches();
        $this->add_options();
        $this->add_matches();
    }

    public function create_content_object()
    {
        $object = new AssessmentMatchingQuestion();
        $values = $this->exportValues();

        $this->set_content_object($object);
        $object->set_display($values[AssessmentMatchingQuestion::PROPERTY_DISPLAY]);
        $this->add_answer();

        return parent::create_content_object();
    }

    public function prepareTabs()
    {
        $this->addDefaultTab();
        $this->addInstructionsTab();
        $this->addMetadataTabs();
    }

    public function setDefaults($defaults = array())
    {
        $object = $this->get_content_object();
        if ($object->get_number_of_options() != 0)
        {
            $options = $object->get_options();
            foreach ($options as $index => $option)
            {
                $defaults[AssessmentMatchingQuestionOption::PROPERTY_VALUE][$index] = $option->get_value();
                $defaults[AssessmentMatchingQuestionOption::PROPERTY_SCORE][$index] = $option->get_score();
                $defaults[self::PROPERTY_DEFAULTS_MATCHES_TO][$index] = $option->get_match();
                $defaults[AssessmentMatchingQuestionOption::PROPERTY_FEEDBACK][$index] = $option->get_feedback();
            }
            $matches = $object->get_matches();
            foreach ($matches as $index => $match)
            {
                $defaults[self::PROPERTY_DEFAULTS_MATCH][$index] = $match;
            }
        }
        else
        {
            $number_of_options = intval($_SESSION[self::PROPERTY_MQ_NUMBER_OF_OPTIONS]);

            for ($option_number = 0; $option_number < $number_of_options; $option_number ++)
            {
                $defaults[AssessmentMatchingQuestionOption::PROPERTY_SCORE][$option_number] = 1;
            }
        }

        $defaults[AssessmentMatchingQuestion::PROPERTY_DISPLAY] = $object->get_display();

        parent::setDefaults($defaults);
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();
        $object->set_display($values[AssessmentMatchingQuestion::PROPERTY_DISPLAY]);

        $this->add_answer();

        return parent::update_content_object();
    }

    /**
     * Updates the session variables to keep track of the current number of options and matches.
     *
     * @todo This code needs some cleaning :)
     */
    public function update_number_of_options_and_matches()
    {
        if (!$this->isSubmitted())
        {
            unset($_SESSION[self::PROPERTY_MQ_NUMBER_OF_OPTIONS]);
            unset($_SESSION[self::PROPERTY_MQ_SKIP_OPTIONS]);
            unset($_SESSION[self::PROPERTY_MQ_NUMBER_OF_MATCHES]);
            unset($_SESSION[self::PROPERTY_MQ_SKIP_MATCHES]);
        }
        if (!isset($_SESSION[self::PROPERTY_MQ_NUMBER_OF_OPTIONS]))
        {
            $_SESSION[self::PROPERTY_MQ_NUMBER_OF_OPTIONS] = 3;
        }
        if (!isset($_SESSION[self::PROPERTY_MQ_SKIP_OPTIONS]))
        {
            $_SESSION[self::PROPERTY_MQ_SKIP_OPTIONS] = array();
        }
        if (isset($_POST[self::PROPERTY_ADD_OPTION]))
        {
            $_SESSION[self::PROPERTY_MQ_NUMBER_OF_OPTIONS] = $_SESSION[self::PROPERTY_MQ_NUMBER_OF_OPTIONS] + 1;
        }
        if (isset($_POST[self::PROPERTY_REMOVE_OPTION]))
        {
            $indexes = array_keys($_POST[self::PROPERTY_REMOVE_OPTION]);
            $_SESSION[self::PROPERTY_MQ_SKIP_OPTIONS][] = $indexes[0];
        }
        if (!isset($_SESSION[self::PROPERTY_MQ_NUMBER_OF_MATCHES]))
        {
            $_SESSION[self::PROPERTY_MQ_NUMBER_OF_MATCHES] = 3;
        }
        if (!isset($_SESSION[self::PROPERTY_MQ_SKIP_MATCHES]))
        {
            $_SESSION[self::PROPERTY_MQ_SKIP_MATCHES] = array();
        }
        if (isset($_POST[self::PROPERTY_ADD_MATCH]))
        {
            $_SESSION[self::PROPERTY_MQ_NUMBER_OF_MATCHES] = $_SESSION[self::PROPERTY_MQ_NUMBER_OF_MATCHES] + 1;
        }
        if (isset($_POST[self::PROPERTY_REMOVE_MATCH]))
        {
            $indexes = array_keys($_POST[self::PROPERTY_REMOVE_MATCH]);
            $_SESSION[self::PROPERTY_MQ_SKIP_MATCHES][] = $indexes[0];
        }
        $object = $this->get_content_object();
        if (!$this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            $_SESSION[self::PROPERTY_MQ_NUMBER_OF_OPTIONS] = $object->get_number_of_options();
            $_SESSION[self::PROPERTY_MQ_NUMBER_OF_MATCHES] = $object->get_number_of_matches();
        }
    }

    public function validate()
    {
        if (isset($_POST[self::PROPERTY_ADD_MATCH]) || isset($_POST[self::PROPERTY_REMOVE_MATCH]) ||
            isset($_POST[self::PROPERTY_REMOVE_OPTION]) || isset($_POST[self::PROPERTY_ADD_OPTION]))
        {
            return false;
        }

        return parent::validate();
    }
}
