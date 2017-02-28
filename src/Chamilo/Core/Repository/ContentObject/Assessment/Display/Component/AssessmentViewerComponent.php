<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentResultProcessor;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentResultViewerForm;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Component\Viewer\AssessmentViewerForm;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * $Id: assessment_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.assessment.component
 */
class AssessmentViewerComponent extends Manager implements DelegateComponent
{
    const FORM_BACK = 'back';
    const FORM_NEXT = 'next';
    const FORM_SUBMIT = 'submit';

    /**
     * The total number of pages for the assessment
     *
     * @var int
     */
    private $total_pages;

    /**
     * An array containing all ComplexContentObjectItem objects for individual questions.
     *
     * @var ComplexContentObjectItem[]
     */
    private $questions;

    /**
     * The form that displays the questions per page
     *
     * @var AssessmentViewerForm
     */
    private $question_form;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        BreadcrumbTrail::getInstance()->add(
            new Breadcrumb(
                $this->get_url(),
                Translation:: get(
                    'AssessmentToolViewerComponent',
                    array('TITLE' => $this->get_root_content_object()->get_title())
                )
            )
        );

        $this->initialize_assessment();

        if ($this->question_form_submitted())
        {
            $result_processor = new AssessmentResultProcessor($this);

            $results_page_number = $this->get_questions_page();

            if (!$this->showFeedbackAfterEveryPage())
            {
                $results_page_number += ($this->get_action() == self :: FORM_NEXT ||
                    $this->get_action() == self :: FORM_SUBMIT) ? - 1 : + 1;
            }

            $result_processor->save_answers($results_page_number);
        }

        if (($this->result_form_submitted() || $this->question_form_submitted()) &&
            $this->get_action() == self :: FORM_SUBMIT
        )
        {
            $result_processor = new AssessmentResultProcessor($this);
            $result_processor->finish_assessment();

            if ($this->get_configuration()->show_feedback_summary())
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = $result_processor->get_results();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
            else
            {
                if($this->get_assessment_back_url())
                {
                    $redirect = new Redirect();
                    $redirect->writeHeader($this->get_assessment_back_url());
                }
            }
        }

        if ($this->question_form_submitted() && $this->showFeedbackAfterEveryPage())
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $result_processor->get_results();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            $this->question_form = new AssessmentViewerForm($this, 'post', $this->get_url());

            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->question_form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Returns the question form
     *
     * @return AssessmentViewerForm
     */
    public function get_question_form()
    {
        return $this->question_form;
    }

    /**
     * Returns the selected assessment
     *
     * @return Assessment
     */
    public function get_assessment()
    {
        return $this->get_root_content_object();
    }

    /**
     * Initializes the assessment for usage in this viewer
     */
    protected function initialize_assessment()
    {
        $assessment = $this->get_root_content_object();

        $this->initialize_questions();
        $total_questions = count($this->questions);

        $questions_per_page = $assessment->get_questions_per_page();

        if ($questions_per_page == 0)
        {
            $this->total_pages = 1;
        }
        else
        {
            $this->total_pages = ceil($total_questions / $questions_per_page);
        }
    }

    /**
     * Initializes the questions for usage in this viewer
     */
    protected function initialize_questions()
    {
        $question_ids = $this->get_parent()->get_registered_question_ids();
        $order_by = array();

        if (!is_array($question_ids) || count($question_ids) == 0)
        {
            $question_ids = $this->get_question_ids_for_assessment();
            $this->get_parent()->register_question_ids($question_ids);
        }

        $order_by[] = new OrderBy(
            new PropertyConditionVariable(
                ComplexContentObjectItem:: class_name(),
                ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER
            )
        );

        $condition = new InCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem:: class_name(),
                ComplexContentObjectItem :: PROPERTY_ID
            ),
            $question_ids
        );

        $this->questions = \Chamilo\Core\Repository\Storage\DataManager:: retrieve_complex_content_object_items(
            ComplexContentObjectItem:: class_name(),
            new DataClassRetrievesParameters($condition, null, null, $order_by)
        )->as_array();
    }

    /**
     * Retrieves the question ids for the assessment
     *
     * @return array
     */
    public function get_question_ids_for_assessment()
    {
        $assessment = $this->get_root_content_object();
        $questions = $assessment->get_questions();

        $question_ids = array();

        while ($question = $questions->next_result())
        {
            $question_ids[] = $question->get_id();
        }

        $number_of_random_questions = $assessment->get_random_questions();
        if ($number_of_random_questions == 0 || count($question_ids) <= $number_of_random_questions)
        {
            return $question_ids;
        }

        $random_question_keys = array_rand($question_ids, $assessment->get_random_questions());

        if (!is_array($random_question_keys))
        {
            $random_question_keys = array($random_question_keys);
        }

        $random_question_ids = array();

        foreach ($random_question_keys as $random_question_key)
        {
            $random_question_ids[] = $question_ids[$random_question_key];
        }

        return $random_question_ids;
    }

    /**
     * Returns the total number of pages
     *
     * @return int
     */
    public function get_total_pages()
    {
        return $this->total_pages;
    }

    /**
     * Returns the questions for this assessment
     *
     * @return \core\repository\storage\data_class\ComplexContentObjectItem[]
     */
    public function get_questions()
    {
        return $this->questions;
    }

    /**
     * Returns the questions for this assessment
     *
     * @param int $page_number
     *
     * @return \core\repository\storage\data_class\ComplexContentObjectItem[]
     */
    public function get_questions_for_page($page_number)
    {
        $assessment = $this->get_root_content_object();
        $questions_per_page = $assessment->get_questions_per_page();

        if ($questions_per_page == 0)
        {
            return $this->questions;
        }

        $page_questions = array();

        $start = (($page_number - 1) * $questions_per_page);
        $stop = $start + $questions_per_page;
        $questions_count = count($this->questions);

        $stop = $stop >= $questions_count ? $questions_count : $stop;

        for ($i = $start; $i < $stop; $i ++)
        {
            $page_questions[] = $this->questions[$i];
        }

        return $page_questions;
    }

    /**
     * Returns the answers for the questions
     */
    public function get_question_answers()
    {
        $answers = array();

        $question_attempts = $this->get_parent()->get_assessment_question_attempts();
        foreach ($question_attempts as $question_attempt)
        {
            $answers[$question_attempt->get_question_complex_id()] = unserialize($question_attempt->get_answer());
        }

        return $answers;
    }

    public function result_form_submitted()
    {
        return !is_null(Request:: post('_qf__' . AssessmentResultViewerForm :: FORM_NAME));
    }

    public function question_form_submitted()
    {
        return !is_null(Request:: post('_qf__' . AssessmentViewerForm :: FORM_NAME));
    }

    public function get_action()
    {
        $actions = array(self :: FORM_NEXT, self :: FORM_SUBMIT, self :: FORM_BACK);

        foreach ($actions as $action)
        {
            if (!is_null(Request:: post($action)))
            {
                return $action;
            }
        }

        return self::FORM_NEXT;
    }

    public function get_questions_page()
    {
        if (!$this->current_page)
        {
            if ($this->result_form_submitted() || $this->question_form_submitted())
            {
                if ($this->question_form_submitted() && $this->showFeedbackAfterEveryPage())
                {
                    // Submitted page number, but results page
                    $this->current_page = $this->get_submitted_page_number();
                }
                else
                {
                    // Submitted page number + 1
                    if ($this->get_action() == 'back')
                    {
                        $this->current_page = $this->get_submitted_page_number() - 1;
                    }
                    else
                    {
                        $this->current_page = $this->get_submitted_page_number() + 1;
                    }
                }
            }
            else
            {
                $this->current_page = 1;
            }
        }

        return $this->current_page;
    }

    public function get_previous_questions_page()
    {
        if (!$this->previous_page)
        {
            if ($this->result_form_submitted() || $this->question_form_submitted())
            {
                $this->previous_page = $this->get_submitted_page_number() - 1;
            }
            else
            {
                $this->previous_page = 1;
            }
        }

        return $this->previous_page;
    }

    public function get_submitted_page_number()
    {
        $regex = '/^(' . AssessmentViewerForm :: PAGE_NUMBER . '|' . AssessmentResultViewerForm :: PAGE_NUMBER .
            ')-([0-9]+)/';
        foreach (array_keys($_REQUEST) as $key)
        {
            if (preg_match($regex, $key, $matches))
            {
                return $matches[2];
            }
        }

        return false;
    }

    public function showFeedbackAfterEveryPage()
    {
        if(!$this->get_configuration()->show_feedback_after_every_page())
        {
            return false;
        }

        /** @var Assessment $assessment */
        $assessment = $this->get_root_content_object();

        if($assessment->get_questions_per_page() == 0)
        {
            return false;
        }

        return $assessment->get_questions_per_page() < count($this->questions);
    }
}
