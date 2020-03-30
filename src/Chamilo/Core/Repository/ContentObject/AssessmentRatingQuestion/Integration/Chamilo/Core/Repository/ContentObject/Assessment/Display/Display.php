<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Display extends QuestionDisplay
{

    public function add_footer()
    {
        $formvalidator = $this->get_formvalidator();

        if ($this->get_question()->has_hint() && $this->get_configuration()->allow_hints())
        {
            $hint_name = 'hint_' . $this->get_complex_content_object_question()->get_id();
            $glyph = new FontAwesomeGlyph('gift', array(), null, 'fas');

            $html[] = '<div class="panel-body panel-body-assessment-hint">';
            $html[] = '<a id="' . $hint_name . '" class="btn btn-default hint_button">' . $glyph->render() . ' ' .
                Translation:: get('GetAHint') . '</a>';
            $html[] = '</div>';

            $footer = implode(PHP_EOL, $html);
            $formvalidator->addElement('html', $footer);
        }

        parent::add_footer();
    }

    public function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $clo_question = $this->get_complex_content_object_question();
        $question = $this->get_question();

        $min = $question->get_low();
        $max = $question->get_high();
        $question_name = $this->get_complex_content_object_question()->get_id() . '_0';
        $scores = array();

        for ($i = $min; $i <= $max; $i ++)
        {
            $scores[$i] = $i;
        }

        $element_template = array();
        $element_template[] =
            '<div class="panel-body"><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);

        $formvalidator->addElement(
            'select', $question_name, Translation::get('Rating') . ': ', $scores, 'class="rating-slider"'
        );
        $renderer->setElementTemplate($element_template, $question_name);
        $formvalidator->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(
                'Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion', true
            ) . 'AssessmentRatingQuestion.min.js'
        )
        );

        $formvalidator->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Assessment::package(), true) . 'GiveHint.js'
        )
        );
    }

    public function needsDescriptionBorder()
    {
        return true;
    }
}
