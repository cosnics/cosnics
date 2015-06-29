<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\Wizard\Inc;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentResultProcessor;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 * $Id: question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc
 */
abstract class AssessmentQuestionResultDisplay
{

    /**
     *
     * @var AssessmentResultProcessor
     */
    private $viewerApplication;

    private $complex_content_object_question;

    private $question;

    private $question_nr;

    private $answers;

    private $score;

    private $hints;

    public function __construct(Application $viewerApplication, $complex_content_object_question, $question_nr, $answers,
        $score, $hints)
    {
        $this->viewerApplication = $viewerApplication;
        $this->complex_content_object_question = $complex_content_object_question;
        $this->question_nr = $question_nr;
        $this->question = $complex_content_object_question->get_ref_object();
        $this->answers = $answers;
        $this->score = $score;
        $this->hints = $hints;
    }

    public function getViewerApplication()
    {
        return $this->viewerApplication;
    }

    public function get_complex_content_object_question()
    {
        return $this->complex_content_object_question;
    }

    public function get_question()
    {
        return $this->question;
    }

    public function get_question_nr()
    {
        return $this->question_nr;
    }

    public function get_answers()
    {
        return $this->answers;
    }

    public function get_score()
    {
        return $this->score;
    }

    public function get_hints()
    {
        return $this->hints;
    }

    public function as_html()
    {
        $html = array();

        $html[] = $this->header();

        if ($this->add_borders())
        {
            $html[] = '<div class="with_borders">';
        }

        $html[] = $this->get_question_result();

        if ($this->add_borders())
        {
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }

        $html[] = $this->footer();
        return implode(PHP_EOL, $html);
    }

    public function get_question_result()
    {
        return $this->get_score() . '<br />';
    }

    public function header()
    {
        $html = array();

        $html[] = '<div class="question">';
        $html[] = '<div class="title">';
        $html[] = '<div class="number">';
        $html[] = '<div class="bevel">';
        $html[] = $this->question_nr . '.';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="text">';

        $html[] = '<div class="bevel" style="float: left;">';
        $html[] = $this->question->get_title();
        $html[] = '</div>';
        $html[] = '<div class="bevel" style="text-align: right;">';

        if ($this->hints > 0)
        {
            $variable = $this->hints == 1 ? 'HintUsed' : 'HintsUsed';
            $label = Translation :: get($variable, array('COUNT' => $this->hints));

            $html[] = '<img style="float: none; vertical-align: baseline;" src="' . Theme :: getInstance()->getImagePath(
                'Chamilo\Core\Repository\ContentObject\Assessment\Display',
                'Buttons/ButtonHint') . '" alt="' . $label . '" title="' . $label . '" />&nbsp;&nbsp;';
        }

        if ($this->getViewerApplication()->get_configuration()->show_score())
        {
            $html[] = $this->get_score() . ' / ' . $this->get_complex_content_object_question()->get_weight();
        }

        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="answer">';

        $description = $this->question->get_description();
        $has_description = StringUtilities :: getInstance()->hasValue($description, true);
        if ($has_description)
        {
            $html[] = '<div class="description">';

            $renderer = new ContentObjectResourceRenderer($this, $description);
            $html[] = $renderer->run();

            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }

        $html[] = '<div class="clear"></div>';

        return implode(PHP_EOL, $html);
    }

    public function footer()
    {
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function add_borders()
    {
        return false;
    }

    public static function factory(Application $viewerApplication, $complex_content_object_question, $question_nr,
        $answers, $score, $hints)
    {
        $class = $complex_content_object_question->get_ref_object()->package() . '\Integration\\' .
             Assessment :: package() . '\Display\ResultDisplay';

        $question_result_display = new $class(
            $viewerApplication,
            $complex_content_object_question,
            $question_nr,
            $answers,
            $score,
            $hints);

        return $question_result_display;
    }
}
