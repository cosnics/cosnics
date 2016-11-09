<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: rating_question.class.php 200 2009-11-13 12:30:04Z kariboe $
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

        $min = $question->get_low();
        $max = $question->get_high();
        $question_name = $this->get_complex_content_object_question()->get_id() . '_0';

        for ($i = $min; $i <= $max; $i ++)
        {
            $scores[$i] = $i;
        }

        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);

        $formvalidator->addElement(
            'select',
            $question_name,
            Translation :: get('Rating') . ': ',
            $scores,
            'class="rating_slider"');
        $renderer->setElementTemplate($element_template, $question_name);
        $formvalidator->addElement(
            'html',
            ResourceManager :: getInstance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    'Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion',
                    true) . 'AssessmentRatingQuestion.js'));

        $formvalidator->addElement(
            'html',
            ResourceManager :: getInstance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(Assessment :: package(), true) . 'GiveHint.js'));
    }

    public function add_borders()
    {
        return true;
    }

    public function get_instruction()
    {
        $instruction = array();
        $question = $this->get_question();

        if ($question->has_description())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('SelectCorrectRating');
            $instruction[] = '</div>';
        }
        else
        {
            $instruction = array();
        }

        return implode(PHP_EOL, $instruction);
    }

    public function add_footer($formvalidator)
    {
        $formvalidator = $this->get_formvalidator();

        if ($this->get_question()->has_hint() && $this->get_configuration()->allow_hints())
        {
            $hint_name = 'hint_' . $this->get_complex_content_object_question()->get_id();

            $html[] = '<div class="splitter">' . Translation :: get('Hint') . '</div>';
            $html[] = '<div class="with_borders"><a id="' . $hint_name .
                 '" class="btn btn-default hint_button"><span class="glyphicon glyphicon-gift"></span> ' .
                 Translation :: get('GetAHint') . '</a></div>';

            $footer = implode(PHP_EOL, $html);
            $formvalidator->addElement('html', $footer);
        }

        parent :: add_footer($formvalidator);
    }
}
