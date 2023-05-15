<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestion;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display
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

        if ($clo_question->get_random())
        {
            $options = $this->shuffle_with_keys($question->get_options());
        }
        else
        {
            $options = $question->get_options();
        }

        $type = $question->get_answer_type();
        $question_id = $clo_question->get_id();
        $answers = [];

        if ($type == AssessmentSelectQuestion::ANSWER_TYPE_RADIO)
        {
            $answers[- 1] = Translation::get('MakeASelection');
        }

        foreach ($options as $key => $option)
        {
            $answers[$key] = $option->get_value();
        }

        $element_template = [];
        $element_template[] =
            '<div><!-- BEGIN error --><small class="text-danger">{error}</small><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clearfix"></div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clearfix"></div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);

        $question_name = $question_id . '_0';

        $formvalidator->addElement('html', '<div class="panel-body">');

        if ($type == AssessmentSelectQuestion::ANSWER_TYPE_CHECKBOX)
        {
            $advanced_select = $formvalidator->createElement(
                'select', $question_name, '', $answers, [
                    'multiple' => 'true',
                    'class' => 'advanced_select_question form-control',
                    'size' => (count($answers) > 10 ? 10 : count($answers))
                ]
            );
            $formvalidator->addElement($advanced_select);
        }
        else
        {
            $formvalidator->addElement(
                'select', $question_name, '', $answers, ['class' => 'select_question form-control']
            );
        }

        $renderer->setElementTemplate($element_template, $question_name);

        $formvalidator->addElement(
            'html', ResourceManager:: getInstance()->getResourceHtml(
            Path:: getInstance()->getJavascriptPath(Assessment::CONTEXT, true) . 'GiveHint.js'
        )
        );

        $formvalidator->addElement('html', '</div>');
    }

    public function needsDescriptionBorder()
    {
        return true;
    }
}
