<?php
namespace Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestion;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestionAnswer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: fill_in_blanks_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_display
 */
class Display extends QuestionDisplay
{

    public function add_question_form()
    {
        $complex_question = $this->get_complex_content_object_question();
        $id = $complex_question->get_id();

        $question = $this->get_question();
        $answers = $question->get_answers();
        $question_type = $question->get_question_type();
        $answer_text = $question->get_answer_text();
        $answer_text = nl2br($answer_text);

        $parts = preg_split(FillInBlanksQuestionAnswer::QUESTIONS_REGEX, $answer_text);

        $this->add_html('<div class="panel-body">');
        $this->add_html('<div class="fill_in_the_blanks_text">');
        $this->add_html(array_shift($parts));

        if ($question->get_show_inline()) // inline
        {
            $element_template = '<div style="display: inline">{element}</div>';
        }
        else
        {
            $element_template = ' {element} ';
        }
        $renderer = $this->get_renderer();
        $renderer->setElementTemplate($element_template, 'select');

        $index = 0;

        $formvalidator = $this->get_formvalidator();

        foreach ($parts as $part)
        {
            $name = $id . '_' . $index;

            if ($question->get_show_inline()) // inline
            {
                $this->add_question($name, $id, $index, $question_type, $answers);
            }
            else // table
            {
                if (count($parts) > 1)
                {
                    $this->add_html(
                        '<span class="fill_in_the_blanks_gap">' .
                        Translation::get(
                            'GapNumber',
                            array('NUMBER' => ($index + 1)),
                            'Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion'
                        ) . '</span>'
                    );
                }
                else
                {
                    $this->add_html('<span class="fill_in_the_blanks_gap">' . Translation::get('Answer') . '</span>');
                }
            }

            $this->add_html($part);
            $index ++;
        }

        $this->add_html('</div>');
        $this->add_html('<div class="clear"></div>');
        $this->add_html('</div>');

        if (!$question->get_show_inline())
        {
            $parts = preg_split(FillInBlanksQuestionAnswer::QUESTIONS_REGEX, $answer_text);
            array_shift($parts);

            $table_header = array();
            $table_header[] =
                '<table class="table table-striped table-bordered table-hover table-data take_assessment">';
            $table_header[] = '<thead>';
            $table_header[] = '<tr>';

            if (count($parts) > 1)
            {
                $table_header[] = '<th class="list"></th>';
                $table_header[] = '<th>' . Translation::get(
                        'Answers',
                        null,
                        ContentObject::get_content_object_type_namespace($question->get_type_name())
                    ) . '</th>';
            }
            else
            {
                $table_header[] = '<th>' . Translation::get(
                        'Answer',
                        null,
                        ContentObject::get_content_object_type_namespace($question->get_type_name())
                    ) . '</th>';
            }

            $table_header[] = '<th>' . Translation::get('Hint') . '</th>';
            $table_header[] = '</tr>';
            $table_header[] = '</thead>';
            $table_header[] = '<tbody>';
            $this->add_html($table_header);

            $index = 0;
            foreach ($parts as $part)
            {
                $name = $id . '_' . $index;
                $this->add_question($name, $id, $index, $question_type, $answers, count($parts) > 1);
                $index ++;
            }

            $table_footer = array();
            $table_footer[] = '</tbody>';
            $table_footer[] = '</table>';
            $this->add_html($table_footer);
        }
        else
        {
            $hint_table = array();
            $hint_table[] =
                '<table class="table table-striped table-bordered table-hover table-data take_assessment" id="hint_table_' .
                $id . '" style="display:none;">';
            $hint_table[] = '<thead>';
            $hint_table[] = '<tr>';
            $hint_table[] = '<th></th>';
            $hint_table[] = '<th>' . Translation::get('Hint') . '</th>';
            $hint_table[] = '</tr>';
            $hint_table[] = '</thead>';
            $hint_table[] = '<tbody>';
            $hint_table[] = '</tbody>';
            $hint_table[] = '</table>';

            $this->add_html(implode(PHP_EOL, $hint_table));
        }

        $formvalidator->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath(
                    ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 7),
                    true
                ) . 'GiveHint.js'
            )
        );
    }

    public function add_html($html)
    {
        $html = is_array($html) ? implode(PHP_EOL, $html) : $html;
        $formvalidator = $this->get_formvalidator();
        $formvalidator->addElement('html', $html);
    }

    public function add_select($name, $options)
    {
        $formvalidator = $this->get_formvalidator();
        $select = $formvalidator->createElement('select', $name, '');

        $select->addOption('-- ' . Translation::get('SelectAnswer') . ' --');
        foreach ($options as $key => $value)
        {
            $select->addOption($value, $key);
        }

        return $select;
    }

    public function add_text($name, $size)
    {
        $formvalidator = $this->get_formvalidator();

        return $formvalidator->createElement(
            'text',
            $name,
            null,
            array('class' => FillInBlanksQuestion::TEXT_INPUT_FIELD_CSS_CLASS, 'size' => $size, 'autocomplete' => 'off')
        );
    }

    public function add_question($name, $id, $index, $question_type, $answers, $multiple_answers)
    {
        $formvalidator = $this->get_formvalidator();

        $group = array();
        if ($multiple_answers)
        {
            $group[] = $formvalidator->createElement('static', null, null, ($index + 1) . '.');
        }

        if ($question_type == FillInBlanksQuestion::TYPE_SELECT)
        {
            // combobox
            $options = $this->get_question_options($index, $answers);
            $group[] = $this->add_select($name, $options);
        }
        else
        {
            $size = $this->get_question()->get_input_field_size($index);
            $group[] = $this->add_text($name, $size);
        }

        $html = array();

        if ($this->get_configuration()->allow_hints())
        {
            if ($question_type != FillInBlanksQuestion::TYPE_SELECT)
            {
                $hint_name = 'hint_' . $this->get_complex_content_object_question()->get_id() . '_' . $index;

                if ($this->get_question()->get_show_inline())
                {
                    $html[] = '<a title="' . Translation::get('GetHint') . '" id="' . $hint_name .
                        '" class="button blanks_hint_button character_hint_button" style="width: 16px; height: 16px; margin: 0px 3px; padding: 0px; background-position: top left; border: 0px; background-color: transparent;">&nbsp;</a>';
                }
                else
                {
                    $html[] = '<a id="' . $hint_name . '" class="button blanks_hint_button character_hint_button">' .
                        Translation::get(
                            'GetHint'
                        ) . '</a>';
                }
            }

            if ($this->get_question()->get_best_answer_for_question($index)->has_hint())
            {

                $hint_name = 'answer_hint_' . $this->get_complex_content_object_question()->get_id() . '_' . $index;

                if ($this->get_question()->get_show_inline())
                {
                    $html[] = '<a title="' . Translation::get('GetAnswerHint') . '" id="' . $hint_name .
                        '" class="button blanks_hint_button" style="width: 16px; height: 16px; margin: 0px 3px; padding: 0px; background-position: top left; border: 0px; background-color: transparent;">&nbsp;</a>';
                }
                else
                {
                    $html[] = '<a id="' . $hint_name . '" class="button blanks_hint_button">' . Translation::get(
                            'GetAnswerHint'
                        ) . '</a>';
                }
            }

            $hint_buttons = implode(" ", $html);
            $hint_buttons = strlen($hint_buttons) > 0 ? ' ' . $hint_buttons : '';

            $group[] = $formvalidator->createElement('static', null, null, $hint_buttons);
        }

        $formvalidator->addGroup($group, 'option_' . $id . '_' . $index, null, '', false);

        $renderer = $this->get_renderer();
        if ($this->get_question()->get_show_inline())
        {
            $renderer->setElementTemplate('{element}', 'option_' . $id . '_' . $index);
            $renderer->setGroupElementTemplate('{element}', 'option_' . $id . '_' . $index);
        }
        else
        {
            $renderer->setElementTemplate(
                '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>',
                'option_' . $id . '_' . $index
            );
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $id . '_' . $index);
        }
    }

    public function get_question_options($index, $answers)
    {
        $result = array();

        foreach ($answers as $answer)
        {
            if ($answer->get_position() == $index)
            {
                $option = $answer->get_value();
                $result[(string) $option] = $option;
            }
        }

        $clo_question = $this->get_complex_content_object_question();
        if ($clo_question->get_random())
        {
            $result = $this->shuffle_with_keys($result);
        }

        return $result;
    }

    public function add_borders()
    {
        return false;
    }

    public function needsDescriptionBorder()
    {
        return true;
    }
}
