<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestion;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Display extends QuestionDisplay
{

    public function add_footer()
    {
        $formvalidator = $this->get_formvalidator();

        if ($this->get_question()->has_hint() && $this->get_configuration()->allow_hints())
        {
            $hint_name = 'hint_' . $this->get_complex_content_object_question()->get_id();
            $glyph = new FontAwesomeGlyph('gift', [], null, 'fas');

            $html[] = '<div class="panel-body panel-body-assessment-hint">';
            $html[] = '<a id="' . $hint_name . '" class="btn btn-default hint_button">' . $glyph->render() . ' ' .
                Translation::get('GetAHint') . '</a>';
            $html[] = '</div>';

            $footer = implode(PHP_EOL, $html);
            $formvalidator->addElement('html', $footer);
        }

        parent::add_footer();
    }

    public function add_question_form()
    {
        $defaults = [];
        $formvalidator = $this->get_formvalidator();
        $clo_question = $this->get_complex_content_object_question();
        $question = $this->get_question();

        if ($clo_question->get_random())
        {
            $answers = $this->shuffle_with_keys($question->get_options());
        }
        else
        {
            $answers = $question->get_options();
        }

        $type = $question->get_answer_type();
        $renderer = $this->get_renderer();

        $table_header = [];
        $table_header[] = '<table class="table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="cell-stat-x2"></th>';
        $table_header[] = '<th>' . $this->get_instruction() . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));

        $question_id = $clo_question->get_id();

        foreach ($answers as $i => $answer)
        {
            $group = [];

            $object_renderer = new ContentObjectResourceRenderer(
                $answer->get_value()
            );

            if ($type == AssessmentMultipleChoiceQuestion::ANSWER_TYPE_RADIO)
            {
                $answer_name = $question_id . '_0';
                $group[] = $formvalidator->createElement('radio', $answer_name, null, null, $i);
                $group[] = $formvalidator->createElement('static', null, null, $object_renderer->run());

                $defaults[$answer_name] = - 1;
            }
            elseif ($type == AssessmentMultipleChoiceQuestion::ANSWER_TYPE_CHECKBOX)
            {
                $answer_name = $question_id . '_' . ($i + 1);
                $group[] = $formvalidator->createElement('checkbox', $answer_name);
                $group[] = $formvalidator->createElement('static', null, null, $object_renderer->run());
            }

            // $formvalidator->addGroup($group, 'option_' . $i, null, '', false);
            $formvalidator->addGroup($group, 'option_' . $question_id . '_' . $i, null, '', false);

            // $renderer->setElementTemplate('<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') .
            // '">{element}</tr>', 'option_' . $i);
            // $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $i);

            $renderer->setElementTemplate(
                '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>',
                'option_' . $question_id . '_' . $i
            );
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $question_id . '_' . $i);
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_footer));

        $formvalidator->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            $formvalidator->getWebPathBuilder()->getJavascriptPath(Assessment::CONTEXT) . 'GiveHint.js'
        )
        );

        $formvalidator->setDefaults($defaults);
    }

    public function get_instruction()
    {
        $question = $this->get_question();
        $type = $question->get_answer_type();

        if ($type == AssessmentMultipleChoiceQuestion::ANSWER_TYPE_RADIO && $question->has_description())
        {
            $title = Translation::get('SelectCorrectAnswer');
        }
        elseif ($type == AssessmentMultipleChoiceQuestion::ANSWER_TYPE_CHECKBOX && $question->has_description())
        {
            $title = Translation::get('SelectCorrectAnswers');
        }
        else
        {
            $title = '';
        }

        return $title;
    }
}
