<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass\AssessmentOpenQuestion;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\ResultViewer\QuestionResultDisplay;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * $Id: assessment_open_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component.result_viewer.question_result_display
 */
class AssessmentOpenQuestionResultDisplay extends QuestionResultDisplay
{

    public function display_question_result()
    {
        $question = $this->get_question();
        $type = $question->get_question_type();
        $answers = $this->get_answers();

        $html = array();

        switch ($type)
        {
            case AssessmentOpenQuestion :: TYPE_OPEN :
                $html[] = $this->display_open($answers[0]);
                break;
            case AssessmentOpenQuestion :: TYPE_OPEN_WITH_DOCUMENT :
                $html[] = $this->display_open($answers[0]);
                $html[] = $this->display_document_box($answers[2], true);
                break;
            case AssessmentOpenQuestion :: TYPE_DOCUMENT :
                $html[] = $this->display_document_box($answers[2]);
                break;
        }

        $html[] = '<div class="splitter" style="margin: -10px; border-left: none; border-right: none; border-top: 1px solid #B5CAE7;">';
        $html[] = Translation :: get('Feedback');
        $html[] = '</div>';

        if ($this->get_results_viewer()->get_configuration()->show_answer_feedback() && ! $this->can_change())
        {
            $object_renderer = new ContentObjectResourceRenderer($this->get_results_viewer(), $question->get_feedback());

            $html[] = $object_renderer->run();
        }

        return implode(PHP_EOL, $html);
    }

    public function add_borders()
    {
        return true;
    }

    public function display_open($answer)
    {
        $html = array();
        $html[] = '<div class="splitter" style="margin: -10px; border-left: none; border-right: none;">';
        $html[] = Translation :: get('Answer');
        $html[] = '</div>';

        $html[] = '<br />';

        if ($answer && trim($answer) != '')
        {
            $html[] = $answer;
        }
        else
        {
            $html[] = '<p>' . Translation :: get('NoAnswer') . '</p>';
        }

        $html[] = '<div class="clear"></div>';
        $html[] = '<br />';
        return implode(PHP_EOL, $html);
    }

    public function display_document_box($answer, $with_open = false)
    {
        $html = array();
        if ($with_open)
        {
            $html[] = '<div class="splitter" style="margin: -10px; border-left: none; border-right: none; border-top: 1px solid #B5CAE7;">';
        }
        else
        {
            $html[] = '<div class="splitter" style="margin: -10px; border-left: none; border-right: none;">';
        }

        $html[] = Translation :: get('Document');
        $html[] = '</div>';

        if (! $answer)
        {

            $html[] = '<br /><p>' . Translation :: get('NoDocument') . '</p><div class="clear"></div><br />';
            return;
        }

        $document = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ContentObject :: class_name(),
            $answer);

        $html[] = '<br />';

        $html[] = '<div style="position: relative; margin: 10px auto; margin-left: -350px; width: 700px;
				  left: 50%; right: 50%; border-width: 1px; border-style: solid;
				  background-color: #E5EDF9; border-color: #4171B5; padding: 15px; text-align:center;">';

        $html[] = sprintf(
            Translation :: get('LPDownloadDocument'),
            $document->get_filename(),
            $document->get_filesize());
        $html[] .= '<br /><a target="about:blank" href="' . \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
            $document->get_id()) . '">' . Translation :: get('Download') . '</a>';

        $html[] = '</div>';
        $html[] = '<br />';
        return implode(PHP_EOL, $html);
    }
}
