<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass\AssessmentMatchingQuestion;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: matching_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_display
 */
class Display extends QuestionDisplay
{

    private $matches;

    private $answers;

    public function add_question_form()
    {
        $clo_question = $this->get_complex_content_object_question();
        $question = $this->get_question();

        if ($clo_question->get_random())
        {
            $this->answers = $this->shuffle_with_keys($question->get_options());
            $this->matches = $this->shuffle_with_keys($question->get_matches());
        }
        else
        {
            $this->answers = $question->get_options();
            $this->matches = $question->get_matches();
        }

        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();

        $table_header = array();

        $table_header[] = '<table class="table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="cell-stat-x2"></th>';
        $table_header[] = '<th>' . Translation::get('PossibleMatches') . '</th>';
        $table_header[] = '<th class="cell-stat" style="text-align:center;">' . Translation::get('MakeASelection') .
             '</th>';

        if ($question->get_display() == AssessmentMatchingQuestion::DISPLAY_LIST)
        {
            $table_header[] = '<th class="cell-stat-x2"></th>';
            $table_header[] = '<th class="cell-stat-x2"></th>';
            $table_header[] = '<th>' . Translation::get('MatchOptionAnswer') . '</th>';
        }

        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';

        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));

        $this->add_options();

        $table_header = array();

        $table_header[] = '</tbody>';
        $table_header[] = '</table>';

        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));
    }

    public function add_options()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $question = $this->get_question();
        $question_id = $this->get_complex_content_object_question()->get_id();

        $matches = $this->matches;
        $answers = $this->answers;

        $options = array();

        $options[- 1] = '-- ' . Translation::get('SelectAnswer') . ' --';
        $match_label = 'A';

        foreach ($matches as $index => $match)
        {
            if ($question->get_display() == AssessmentMatchingQuestion::DISPLAY_LIST)
            {
                $options[$index] = $match_label;
            }
            else
            {
                $options[$index] = strip_tags($match);
            }
            $match_label ++;
        }

        if ($question->get_display() == AssessmentMatchingQuestion::DISPLAY_LIST)
        {
            $maximum = count($matches) > count($answers) ? count($matches) : count($answers);
        }
        else
        {
            $maximum = count($answers);
        }

        $match_label = 'A';
        for ($i = 0; $i < $maximum; $i ++)
        {
            $formvalidator->addElement('html', '<tr>');

            $answer = current($answers);
            $answerIndex = key($answers);
            next($answers);

            if ($answer)
            {
                $answer_number = ($i + 1) . '.';
                $answer_name = $question_id . '_' . $answerIndex;

                $formvalidator->addElement('html', '<td>' . $answer_number . '</td>');

                $object_renderer = new ContentObjectResourceRenderer(
                    $this->get_formvalidator()->get_assessment_viewer(),
                    $answer->get_value());
                $formvalidator->addElement('html', '<td>' . $object_renderer->run() . '</td>');
                $formvalidator->addElement('select', $answer_name, null, $options);
                $renderer->setElementTemplate('<td>{element}</td>', $answer_name);
            }
            else
            {
                $formvalidator->addElement('html', '<td colspan="3"></td>');
            }

            if ($question->get_display() == AssessmentMatchingQuestion::DISPLAY_LIST)
            {
                $formvalidator->addElement('html', '<td></td>');

                $match = current($matches);
                next($matches);

                if ($match)
                {
                    $formvalidator->addElement('html', '<td style="width: 10px">' . $match_label . '.</td>');

                    $object_renderer = new ContentObjectResourceRenderer(
                        $this->get_formvalidator()->get_assessment_viewer(),
                        $match);
                    $formvalidator->addElement('html', '<td>' . $object_renderer->run() . '</td>');
                }
                else
                {
                    if($i <= count($matches))
                    {
                        $formvalidator->addElement('html', '<td colspan="2" rowspan="' . ($maximum - $i) . '"></td>');
                    }
                }
            }

            $formvalidator->addElement('html', '</tr>');

            $renderer->setElementTemplate(
                '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>',
                'match_' . $i);

            $match_label ++;
        }
    }

    public function get_instruction()
    {
        return Translation::get('SelectCorrectAnswers');
    }
}
