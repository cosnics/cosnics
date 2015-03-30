<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: result_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component
 */
class ResultsViewerComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $form = new FormValidator('result_viewer', 'post', $this->get_url());

        $results = $this->get_parent()->retrieve_assessment_results();
        $question_cids = array_keys($results);

        if (count($question_cids) <= 0)
        {
            throw new \Exception(Translation :: get('ThisAssessmentHasNoAnswers'));
        }

        $condition = new InCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem :: class_name(),
                ComplexContentObjectItem :: PROPERTY_ID),
            $question_cids,
            ComplexContentObjectItem :: get_table_name());

        $questions_cloi = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(),
            $condition);

        $assessment = $this->get_root_content_object();
        $form->add_information_message('information', $assessment->get_title(), $assessment->get_description(), true);
        $form->addElement('html', '<br />');

        $total_score = 0;
        $total_weight = 0;
        $question_number = 1;

        while ($question_cloi = $questions_cloi->next_result())
        {
            $result = $results[$question_cloi->get_id()];

            $question = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
                $question_cloi->get_ref());
            $answers = unserialize($result['answer']);
            $feedback = $result['feedback'];

            $question_cloi->set_ref_object($question);

            $score = $result['score'];
            $score = round($score * 100) / 100;

            $total_score += $score;
            $total_weight += $question_cloi->get_weight();

            $display = QuestionResultDisplay :: factory(
                $this,
                $form,
                $question_cloi,
                $question_number,
                $answers,
                $score,
                $result['hint'],
                $feedback,
                $this->get_parent()->can_change_answer_data());
            $display->render();

            $question_number ++;
        }

        if ($form->validate())
        {
            $values = $form->exportValues();

            $question_forms = array();
            foreach ($values as $key => $value)
            {
                $split = split('_', $key);
                if (is_numeric($split[0]))
                {
                    $question_forms[$split[0]][$split[1]] = $value;
                }
            }

            $total_score = 0;

            foreach ($question_forms as $question_id => $question_form)
            {
                $score = $question_form['score'];
                $feedback = $question_form['feedback'];

                $this->change_answer_data($question_id, $score, $feedback);

                $total_score += $score;
            }

            $percent = round(($total_score / $total_weight) * 100);
            $this->change_total_score($percent);
        }

        if ($this->get_configuration()->show_score())
        {
            $html = array();

            $html[] = '<div class="question">';
            $html[] = '<div class="title">';
            $html[] = '<div class="text">';
            $html[] = '<div class="bevel" style="float: left;">';
            $html[] = Translation :: get('TotalScore');
            $html[] = '</div>';
            $html[] = '<div class="bevel" style="text-align: right;">';

            if ($total_score < 0)
            {
                $total_score = 0;
            }

            $percent = round(($total_score / $total_weight) * 100);

            $html[] = $total_score . ' / ' . $total_weight . ' (' . $percent . '%)';
            $html[] = '</div>';

            $html[] = '</div></div></div>';
            $html[] = '<div class="clear"></div>';

            $form->addElement('html', implode(PHP_EOL, $html));
        }

        $buttons[] = $form->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive'));
        $buttons[] = $form->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        if ($this->get_parent()->can_change_answer_data())
        {
            $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $form->toHtml();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
