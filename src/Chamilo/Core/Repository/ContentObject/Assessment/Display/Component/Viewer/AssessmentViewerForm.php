<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\AssessmentViewerComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class AssessmentViewerForm extends FormValidator
{
    public const FORM_NAME = 'assessment_viewer_form';
    public const PAGE_NUMBER = 'assessment_page_number';

    /**
     * @var AssessmentViewerComponent
     */
    private $assessment_viewer;

    private $questions;

    public function __construct(
        AssessmentViewerComponent $assessment_viewer, $method = self::FORM_METHOD_POST, $action = null
    )
    {
        parent::__construct(self::FORM_NAME, $method, $action);

        $this->assessment_viewer = $assessment_viewer;

        $this->add_general();
        $this->add_buttons();
        $this->add_questions();
        $this->add_answers_from_question_attempts();
        $this->add_buttons();
    }

    /**
     * Formats and adds a single answer to the form defaults
     *
     * @param int $question_cid
     * @param mixed[] $answer
     * @param mixed[] $defaults
     */
    public function add_answer_to_form_defaults($question_cid, $answer, &$defaults)
    {
        $answer = $this->multi_dimensional_array_to_single_dimensional_array($answer);

        foreach ($answer as $option_index => $option_answer)
        {
            $defaults[$question_cid . '_' . $option_index] = $option_answer;
        }
    }

    /**
     * Adds the answers from the question attempts to the default values of this form so that the form remembers what
     * you have filled in when you navigate through a multi page assessment
     */
    public function add_answers_from_question_attempts()
    {
        $defaults = [];

        $answers = $this->get_assessment_viewer()->get_question_answers();
        foreach ($answers as $question_cid => $answer)
        {
            $this->add_answer_to_form_defaults($question_cid, $answer, $defaults);
        }

        $this->setConstants($defaults);
    }

    public function add_buttons()
    {
        $this->get_page_number();
        $this->get_total_pages();

        // Add submit button if there is at least one question
        if (count($this->questions) > 0)
        {
            $submit_button = $this->createElement(
                'style_submit_button', 'submit', Translation::get('Submit', null, StringUtilities::LIBRARIES),
                ['style' => 'display: none;']
            );
        }

        if ($this->assessment_viewer->showFeedbackAfterEveryPage())
        {
            $buttons[] = $this->createElement(
                'style_button', 'next', Translation::get('Check', null, StringUtilities::LIBRARIES), null, null,
                new FontAwesomeGlyph('chevron-right')
            );
        }
        else
        {
            if ($this->get_page_number() > 1)
            {
                $buttons[] = $this->createElement(
                    'style_button', 'back', Translation::get('Previous', null, StringUtilities::LIBRARIES), null, null,
                    new FontAwesomeGlyph('chevron-left')
                );
            }

            if ($this->get_page_number() < $this->get_total_pages())
            {
                $buttons[] = $this->createElement(
                    'style_button', 'next', Translation::get('Next', null, StringUtilities::LIBRARIES), null, null,
                    new FontAwesomeGlyph('chevron-right')
                );
            }
            elseif ($submit_button)
            {
                $submit_button->_attributes['style'] = '';
            }
        }

        if ($submit_button)
        {
            $buttons[] = $submit_button;
        }

        if (count($buttons) > 0)
        {
            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }

        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<div style="float: right;">{element}</div><br /><br />', 'buttons');
        $renderer->setGroupElementTemplate('{element}', 'buttons');
    }

    public function add_general()
    {
        $current_page = self::PAGE_NUMBER . '-' . $this->get_page_number();
        $assessment = $this->assessment_viewer->get_assessment();

        $this->addElement('hidden', $current_page, $this->get_page_number());

        if ($assessment->has_description())
        {
            $display = ContentObjectRenditionImplementation::factory(
                $assessment, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_FULL,
                $this->assessment_viewer
            );

            $this->addElement('html', $display->render());
        }

        $this->addElement('hidden', 'start_time', '', ['id' => 'start_time']);
        $this->addElement('hidden', 'max_time', '', ['id' => 'max_time']);
        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\Assessment') .
            'AssessmentViewer.js'
        )
        );

        $start_time = Request::post('start_time');
        $start_time = $start_time ?: 0;

        $defaults['start_time'] = $start_time;
        $defaults['max_time'] = ($assessment->get_maximum_time() * 60);
        $this->setDefaults($defaults);

        $current_time = $defaults['max_time'] - $defaults['start_time'];

        if ($defaults['max_time'] > 0)
        {
            $this->addElement(
                'html', '<div class="alert alert-warning time_left">' . Translation::get('TimeLeft') .
                ': <strong><div class="time">' . $current_time . '</div>' . Translation::get('SecondsShort') .
                '</div></strong>'
            );
        }

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Libraries') . 'HeartBeat.js'
        )
        );
    }

    public function add_questions()
    {
        $i =
            (($this->get_page_number() - 1) * $this->assessment_viewer->get_assessment()->get_questions_per_page()) + 1;

        $this->questions = $this->assessment_viewer->get_questions_for_page($this->get_page_number());

        foreach ($this->questions as $question)
        {
            $question_display = QuestionDisplay::factory($this, $question, $i);
            $question_display->render();

            $i ++;
        }
    }

    public function get_assessment_viewer()
    {
        return $this->assessment_viewer;
    }

    public function get_page_number()
    {
        return $this->assessment_viewer->get_questions_page();
    }

    public function get_total_pages()
    {
        return $this->assessment_viewer->get_total_pages();
    }
}
