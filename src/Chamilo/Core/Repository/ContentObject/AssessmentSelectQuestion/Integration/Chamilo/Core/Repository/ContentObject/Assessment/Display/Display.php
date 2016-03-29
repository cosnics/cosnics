<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Integration\Chamilo\Core\Repository\ContentObject\Assessment\Display;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\QuestionDisplay;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestion;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: select_question.class.php 200 2009-11-13 12:30:04Z kariboe $
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
        }
        else
        {
            $options = $question->get_options();
        }

        $type = $question->get_answer_type();
        $question_id = $clo_question->get_id();

        $answers = array();

        if ($type == AssessmentSelectQuestion :: ANSWER_TYPE_RADIO)
        {
            $answers[- 1] = Translation :: get('MakeASelection');
        }

        foreach ($options as $key => $option)
        {
            $answers[$key] = $option->get_value();
        }

        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode(PHP_EOL, $element_template);

        $question_name = $question_id . '_0';

        if ($type == AssessmentSelectQuestion :: ANSWER_TYPE_CHECKBOX)
        {
            $advanced_select = $formvalidator->createElement(
                'advmultiselect',
                $question_name,
                '',
                $answers,
                array('style' => 'width: 200px;', 'class' => 'advanced_select_question'));
            $advanced_select->setButtonAttributes('add', 'class="add"');
            $advanced_select->setButtonAttributes('remove', 'class="remove"');
            $formvalidator->addElement($advanced_select);
        }
        else
        {
            $formvalidator->addElement('select', $question_name, '', $answers, 'class="select_question"');
        }

        $renderer->setElementTemplate($element_template, $question_name);

        $formvalidator->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(
                    ClassnameUtilities :: getInstance()->getNamespaceParent(
                        'Chamilo\Core\Repository\ContentObject\Assessment')) . 'GiveHint.js'));
    }

    public function add_borders()
    {
        return true;
    }

    public function get_instruction()
    {
        $instruction = array();
        $question = $this->get_question();
        $type = $question->get_answer_type();

        if ($type == AssessmentSelectQuestion :: ANSWER_TYPE_RADIO && $question->has_description())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('SelectCorrectAnswer');
            $instruction[] = '</div>';
        }
        elseif ($type == AssessmentSelectQuestion :: ANSWER_TYPE_CHECKBOX && $question->has_description())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('SelectCorrectAnswers');
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
                 '" class="btn btn-default"><span class="glyphicon glyphicon-gift"></span> ' .
                 Translation :: get('GetAHint') . '</a></div>';

            $footer = implode(PHP_EOL, $html);
            $formvalidator->addElement('html', $footer);
        }

        parent :: add_footer($formvalidator);
    }
}
