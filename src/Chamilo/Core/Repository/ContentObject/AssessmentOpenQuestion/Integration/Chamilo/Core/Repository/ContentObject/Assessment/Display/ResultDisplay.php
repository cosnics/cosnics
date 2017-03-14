<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\AnswerFeedbackDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentQuestionResultDisplay;
use Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass\AssessmentOpenQuestion;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package
 *          core\repository\content_object\assessment_open_question\integration\core\repository\content_object\assessment\display
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ResultDisplay extends AssessmentQuestionResultDisplay
{

    public function get_question_result()
    {
        $question = $this->get_question();
        $type = $question->get_question_type();
        $answers = $this->get_answers();
        $configuration = $this->getViewerApplication()->get_configuration();

        $html = array();

        switch ($type)
        {
            case AssessmentOpenQuestion::TYPE_OPEN :
                $html[] = $this->display_open($answers[0]);
                break;
            case AssessmentOpenQuestion::TYPE_OPEN_WITH_DOCUMENT :
                $html[] = $this->display_open($answers[0]);
                $html[] = $this->display_document_box($answers[2], true);
                break;
            case AssessmentOpenQuestion::TYPE_DOCUMENT :
                $html[] = $this->display_document_box($answers[2]);
                break;
        }

        $html[] = '<div style="padding: 0 10px 10px 10px; border-top: 1px dashed #dddddd;">';
        $html[] = '<h5>' . Translation::get('Feedback') . '</h5>';

        if (AnswerFeedbackDisplay::allowed($configuration, $this->get_complex_content_object_question(), true, true))
        {
            $object_renderer = new ContentObjectResourceRenderer(
                $this->getViewerApplication(),
                $question->get_feedback()
            );

            $feedback = trim($object_renderer->run());

            if (empty($feedback))
            {
                $feedback = '<p>' . Translation::getInstance()->getTranslation(
                        'NoFeedback', null, 'Chamilo\Core\Repository\ContentObject\Assessment\Display'
                    ) . '</p>';
            }

            $html[] = $feedback;
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function add_borders()
    {
        return true;
    }

    public function display_open($answer)
    {
        $html = array();
        $html[] = '<div style="padding: 0 10px 10px 10px;">';
        $html[] = '<h5>' . Translation::get('Answer') . '</h5>';

        if ($answer && trim($answer) != '')
        {
            $html[] = $answer;
        }
        else
        {
            $html[] = '<p>' . Translation::get('NoAnswer') . '</p>';
        }

        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function display_document_box($answer, $with_open = false)
    {
        $html = array();
        if ($with_open)
        {
            $html[] = '<div style="padding: 0 10px 10px 10px;">';
        }
        else
        {
            $html[] = '<div style="padding: 0 10px 10px 10px;">';
        }

        $html[] = '<h5>' . Translation::get('Document') . '</h5>';

        if (!$answer)
        {

            $html[] = '<p>' . Translation::get('NoDocument') . '</p><div class="clear"></div><br />';

            return;
        }

        $document = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(ContentObject::class_name(), $answer);

        $html[] = '<br />';

        $html[] = '<div style="position: relative; margin: 10px auto; margin-left: -350px; width: 700px;
				  left: 50%; right: 50%; border-width: 1px; border-style: solid;
				  background-color: #E5EDF9; border-color: #4171B5; padding: 15px; text-align:center;">';

        $html[] = sprintf(Translation::get('LPDownloadDocument'), $document->get_filename(), $document->get_filesize());
        $html[] .= '<br /><a target="about:blank" href="' .
            \Chamilo\Core\Repository\Manager::get_document_downloader_url(
                $document->get_id(),
                $document->calculate_security_code()
            ) . '">' . Translation::get('Download') . '</a>';

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function needsDescriptionBorder()
    {
        return true;
    }
}
