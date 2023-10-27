<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass\AssessmentMatrixQuestion;

/**
 *
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_display
 */
class Display extends QuestionDisplay
{

    public function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $clo_question = $this->get_complex_content_object_question();
        $question = $this->get_question();

        if ($clo_question->get_random())
        {
            $options = $this->shuffle_with_keys($question->get_options());
            $matches = $this->shuffle_with_keys($question->get_matches());
        }
        else
        {
            $options = $question->get_options();
            $matches = $question->get_matches();
        }

        $type = $question->get_matrix_type();

        $table_header = array();
        $table_header[] = '<table class="table take_assessment take_assessment_matrix_question">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="cell-stat-3x"></th>';

        foreach ($matches as $match)
        {
            $table_header[] = '<th class="text-center">' . $match . '</th>';
        }

        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_header));

        $question_id = $clo_question->get_id();

        $defaults = [];

        foreach ($options as $i => $option)
        {
            $group = array();

            $object_renderer = new ContentObjectResourceRenderer(
                $this->get_formvalidator()->get_assessment_viewer(),
                $option->get_value());

            $group[] = $formvalidator->createElement(
                'static',
                null,
                null,
                '<div style="text-align: left;">' . $object_renderer->run() . '</div>');

            foreach ($matches as $j => $match)
            {
                if ($type == AssessmentMatrixQuestion::MATRIX_TYPE_RADIO)
                {
                    $answer_name = $question_id . '_' . $i;
                    $group[] = $formvalidator->createElement('radio', $answer_name, null, null, $j);
                    $defaults[$answer_name] = -1;
                }
                elseif ($type == AssessmentMatrixQuestion::MATRIX_TYPE_CHECKBOX)
                {
                    $answer_name = $question_id . '_' . $i . '[' . $j . ']';
                    $group[] = $formvalidator->createElement('checkbox', $answer_name);
                }
            }

            $formvalidator->addGroup($group, 'matrix_option_' . $question_id . '_' . $i, null, '', false);

            $renderer->setElementTemplate('<tr>{element}</tr>', 'matrix_option_' . $question_id . '_' . $i);
            $renderer->setGroupElementTemplate(
                '<td class="text-center">{element}</td>',
                'matrix_option_' . $question_id . '_' . $i);
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode(PHP_EOL, $table_footer));

        $formvalidator->setDefaults($defaults);
    }

    public function add_border()
    {
        return false;
    }
}
