<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer;

use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class AssessmentResultViewerForm extends FormValidator
{
    const FORM_NAME = 'assessment_result_viewer_form';
    const PAGE_NUMBER = 'assessment_result_page_number';

    /**
     *
     * @var AssessmentResultProcessor
     */
    private $assessment_result_processor;

    public function __construct(AssessmentResultProcessor $assessment_result_processor, $method = self::FORM_METHOD_POST, $action = null
    )
    {
        parent::__construct('assessment_result_viewer_form', $method, $action);

        $this->assessment_result_processor = $assessment_result_processor;

        $this->add_general();
        $this->add_buttons();
        $this->add_results();
        $this->add_buttons();
    }

    public function add_buttons()
    {
        $assessmentViewer = $this->assessment_result_processor->get_assessment_viewer();
        $assesmentConfiguration = $assessmentViewer->get_configuration();

        $back_url = $assessmentViewer->get_assessment_back_url();

        $finished = $this->assessment_result_processor->is_finished();

        if (!$finished && $this->get_page_number() != ($this->get_total_pages() + 1))
        {
            // $progress = round(($this->get_page_number() / $this->get_total_pages()) * 100);
            // Display::get_progress_bar($progress)
            // TODO: Temporary fix
            $this->get_page_number();
            $this->get_total_pages();

            // $this->addElement('html', '<div style="float: left; padding: 7px; font-weight: bold; line-height:
            // 100%;">' . Translation::get('PageNumberOfTotal', array(
            // 'CURRENT' => $this->get_page_number(),
            // 'TOTAL' => $this->get_total_pages())) . '</div>');
        }

        if (!$finished &&
            ($this->get_page_number() < $this->assessment_result_processor->get_assessment_viewer()->get_total_pages()))
        {
            $buttons[] = $this->createElement(
                'style_button', 'next', Translation::get('Next', null, Utilities::COMMON_LIBRARIES), null, null,
                new FontAwesomeGlyph('chevron-right')
            );
        }
        elseif (!$finished &&
            $this->get_page_number() == $this->assessment_result_processor->get_assessment_viewer()->get_total_pages())
        {

            if ($assesmentConfiguration->show_feedback_summary())
            {
                if ($assesmentConfiguration->show_score())
                {
                    $buttons[] = $this->createElement('style_submit_button', 'submit', Translation:: get('SeeResults'));
                }
                else
                {
                    $buttons[] =
                        $this->createElement('style_submit_button', 'submit', Translation:: get('ViewResults'));
                }
            }
            else
            {
                $buttons[] = $this->createElement(
                    'style_submit_button', 'submit', Translation:: get('Finish'), array('class' => 'btn-danger'), null,
                    new FontAwesomeGlyph('stop')
                );
            }
        }
        elseif ($finished || $this->get_page_number() == ($this->get_total_pages() + 1))
        {
            $continue_url = $this->assessment_result_processor->get_assessment_viewer()->get_assessment_continue_url();
            $current_url = $this->assessment_result_processor->get_assessment_viewer()->get_assessment_current_url();

            if ($this->assessment_result_processor->get_assessment_viewer()->get_root_content_object()
                ->has_unlimited_attempts())
            {
                $glyph = new FontAwesomeGlyph('sync', [], null, 'fas');
                $buttons[] = $this->createElement(
                    'static', null, null,
                    '<a href="' . $current_url . '" class="btn btn-default" target="_parent">' . $glyph->render() .
                    ' ' . Translation::get('DoAssessmentAgain') . '</a>'
                );
            }

            if (!StringUtilities::getInstance()->isNullOrEmpty($back_url))
            {
                $glyph = new FontAwesomeGlyph('stop', [], null, 'fas');

                $buttons[] = $this->createElement(
                    'static', null, null,
                    '<a href="' . $back_url . '" class="btn btn-danger" target="_parent">' . $glyph->render() . ' ' .
                    Translation::get('Finish') . '</a>'
                );
            }

            if (!StringUtilities::getInstance()->isNullOrEmpty($continue_url))
            {
                $glyph = new FontAwesomeGlyph(
                    'check-circle', [], null, 'fas'
                );

                $buttons[] = $this->createElement(
                    'static', null, null,
                    '<a href="' . $continue_url . '" class="btn btn-default" target="_parent">' . $glyph->render() .
                    ' ' . Translation::get('ContinueSession') . '</a>'
                );
            }
        }

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<div style="float: right;">{element}</div><br /><br />', 'buttons');
        $renderer->setGroupElementTemplate('{element}', 'buttons');
    }

    public function add_general()
    {
        $current_page = self::PAGE_NUMBER . '-' . $this->get_page_number();
        $this->addElement('hidden', $current_page, $this->get_page_number());

        $start_time = Request::post('start_time');
        $start_time = $start_time ? $start_time : 0;

        $this->addElement('hidden', 'start_time', $start_time, array('id' => 'start_time'));
    }

    public function add_results()
    {
        $question_results = $this->assessment_result_processor->get_question_results();
        $question_results = implode(PHP_EOL, $question_results);
        $this->addElement('html', $question_results);
    }

    public function get_page_number()
    {
        return $this->assessment_result_processor->get_assessment_viewer()->get_questions_page();
    }

    public function get_total_pages()
    {
        return $this->assessment_result_processor->get_assessment_viewer()->get_total_pages();
    }
}
