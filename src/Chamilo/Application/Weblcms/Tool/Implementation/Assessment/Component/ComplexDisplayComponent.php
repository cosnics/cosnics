<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Assessment\Display\Interfaces\AssessmentDisplaySupport;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application\weblcms\tool\implementation\assessment
 * @author Previous Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Cleanup
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ComplexDisplayComponent extends Manager implements AssessmentDisplaySupport, DelegateComponent
{
    /**
     * @var ContentObjectPublication
     */
    private $publication;

    private $assessment;

    private $publication_id;

    /**
     * The assessmnet attempt
     *
     * @var AssessmentAttempt
     */
    private $assessment_attempt;

    /**
     * The question attempt
     *
     * @var QuestionAttempt[]
     */
    private $question_attempts;

    public function run()
    {
        // Retrieving assessment
        if (Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID))
        {
            $this->publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

            $this->set_parameter(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $this->publication_id
            );

            $this->publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                ContentObjectPublication::class_name(),
                $this->publication_id
            );

            if (!$this->publication || !$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication))
            {
                $this->redirect(
                    Translation::get("NotAllowed", null, Utilities::COMMON_LIBRARIES),
                    true,
                    array(),
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                    )
                );
            }

            $this->assessment = $this->publication->get_content_object();
            $this->set_parameter(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $this->publication_id
            );
        }

        // Checking statistics

        $track = new AssessmentAttempt();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable($this->publication_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt::class_name(), AssessmentAttempt::PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id())
        );
        $condition = new AndCondition($conditions);

        $trackers = DataManager::retrieves(
            AssessmentAttempt::class_name(),
            new DataClassRetrievesParameters($condition)
        );

        $count = $trackers->size();

        while ($tracker = $trackers->next_result())
        {
            if ($tracker->get_status() == AssessmentAttempt::STATUS_NOT_COMPLETED)
            {
                $this->assessment_attempt = $tracker;
                $count --;
                break;
            }
        }

        if ($this->assessment->get_maximum_attempts() != 0 && $count >= $this->assessment->get_maximum_attempts())
        {
            return $this->display_error_page(Translation::get('YouHaveReachedYourMaximumAttempts'));
        }

        if (!$this->assessment_attempt)
        {
            $this->assessment_attempt = $this->create_assessment_attempt();
        }

        // Executing assessment

        if ($this->assessment->get_type() == Hotpotatoes::class_name())
        {
            $html = array();

            $html[] = $this->render_header();

            $redirect = new Redirect(
                array(
                    \Chamilo\Application\Weblcms\Manager::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Ajax\Manager::context(
                    ),
                    \Chamilo\Application\Weblcms\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Ajax\Manager::ACTION_SAVE_HOTPOTATOES_SCORE
                )
            );

            $path = $this->assessment->add_javascript(
                $redirect->getUrl(),
                $this->get_assessment_back_url(),
                $this->assessment_attempt->get_id()
            );

            $html[] = '<iframe src="' . $path . '" width="100%" height="600">
  				 <p>Your browser does not support iframes.</p>
				 </iframe>';

            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            if ($this->assessment->count_questions() == 0)
            {
                $this->redirect(
                    Translation::get("EmptyAssessment"),
                    true,
                    array(),
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION,
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID
                    )
                );
            }

            $context = $this->assessment->package() . '\Display';
            $factory = new ApplicationFactory(
                $context,
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            );

            return $factory->run();
        }
    }

    /**
     * Returns the root content object for the complex display
     *
     * @return Assessment
     */
    public function get_root_content_object()
    {
        return $this->assessment;
    }

    /**
     * Creates a new assessment attempt
     *
     * @return AssessmentAttempt
     */
    public function create_assessment_attempt()
    {
        $attempt = new AssessmentAttempt();
        $attempt->set_assessment_id($this->publication_id);
        $attempt->set_user_id($this->get_user_id());
        $attempt->set_course_id($this->get_course_id());
        $attempt->set_total_score(0);
        $attempt->set_start_time(time());

        if ($attempt->create())
        {
            return $attempt;
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns the id of the current assessment attempt
     *
     * @return int
     */
    public function get_assessment_current_attempt_id()
    {
        return $this->assessment_attempt->get_id();
    }

    /**
     * Saves the assessment answer of a question to the database
     *
     * @param int $complex_question_id
     * @param string $answer
     * @param int $score
     */
    public function save_assessment_answer($complex_question_id, $answer, $score, $hint)
    {
        $question_attempt = $this->get_assessment_question_attempt($complex_question_id);

        $question_attempt->set_answer($answer);
        $question_attempt->set_score($score);
        $question_attempt->set_hint($hint);

        $question_attempt->update();
    }

    /**
     * Saves the result of the assessment to the database
     *
     * @param int $total_score
     */
    public function save_assessment_result($total_score)
    {
        $assessment_attempt = $this->assessment_attempt;

        $assessment_attempt->set_total_score($total_score);
        $assessment_attempt->set_end_time(time());
        $assessment_attempt->set_status(AssessmentAttempt::STATUS_COMPLETED);

        $assessment_attempt->set_total_time(
            $assessment_attempt->get_total_time() +
            ($assessment_attempt->get_end_time() - $assessment_attempt->get_start_time())
        );

        $assessment_attempt->update();
    }

    /**
     * Returns the assessment question attempts
     *
     * @return QuestionAttempt[]
     */
    public function get_assessment_question_attempts()
    {
        if (is_null($this->question_attempts))
        {
            $this->question_attempts = $this->retrieve_question_attempts();
        }

        return $this->question_attempts;
    }

    /**
     * Retrieves the question attempts for the selected assessment attempt
     *
     * @return QuestionAttempt[]
     */
    protected function retrieve_question_attempts()
    {
        $question_attempts = array();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                QuestionAttempt::class_name(),
                QuestionAttempt::PROPERTY_ASSESSMENT_ATTEMPT_ID
            ),
            new StaticConditionVariable($this->assessment_attempt->get_id())
        );

        $question_attempts_result_set = DataManager::retrieves(
            QuestionAttempt::class_name(),
            new DataClassRetrievesParameters($condition)
        );

        while ($question_attempt = $question_attempts_result_set->next_result())
        {
            $question_attempts[$question_attempt->get_question_complex_id()] = $question_attempt;
        }

        return $question_attempts;
    }

    /**
     * Registers the question ids
     *
     * @param int[] $question_ids
     */
    public function register_question_ids($question_ids)
    {
        foreach ($question_ids as $complex_question_id)
        {
            $question_attempt = new QuestionAttempt();
            $question_attempt->set_assessment_attempt_id($this->assessment_attempt->get_id());
            $question_attempt->set_question_complex_id($complex_question_id);
            $question_attempt->set_answer('');
            $question_attempt->set_score(0);
            $question_attempt->set_feedback('');
            $question_attempt->set_hint(0);

            if ($question_attempt->create())
            {
                $this->question_attempts[$complex_question_id] = $question_attempt;
            }
        }
    }

    /**
     * Returns the registered question ids
     *
     * @return int[] $question_ids
     */
    public function get_registered_question_ids()
    {
        $question_ids = array();

        $question_attempts = $this->get_assessment_question_attempts();
        foreach ($question_attempts as $question_attempt)
        {
            $question_ids[] = $question_attempt->get_question_complex_id();
        }

        return $question_ids;
    }

    /**
     * Gets a single question attempt by a given question id
     *
     * @param int $complex_question_id
     *
     * @return QuestionAttempt
     */
    public function get_assessment_question_attempt($complex_question_id)
    {
        return $this->question_attempts[$complex_question_id];
    }

    public function get_assessment_continue_url()
    {
        return null;
    }

    public function get_assessment_back_url()
    {
        return $this->get_url(
            array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE),
            array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID)
        );
    }

    public function get_assessment_configuration()
    {
        $parameters = new DataClassRetrieveParameters(
            new EqualityCondition(
                new PropertyConditionVariable(Publication::class_name(), Publication::PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($this->publication->get_id())
            )
        );
        $assessment_publication = DataManager::retrieve(Publication::class_name(), $parameters);

        return $assessment_publication->get_configuration();
    }

    public function get_assessment_parameters()
    {
        return array();
    }

    public function get_assessment_current_url()
    {
        return $this->get_url();
    }

    // METHODS FOR COMPLEX DISPLAY RIGHTS
    public function is_allowed_to_edit_content_object()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication) &&
        $this->publication->get_allow_collaboration();
    }

    public function is_allowed_to_view_content_object()
    {
        return $this->is_allowed(WeblcmsRights::VIEW_RIGHT, $this->publication);
    }

    public function is_allowed_to_add_child()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_child()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_delete_feedback()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    public function is_allowed_to_edit_feedback()
    {
        return $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $this->publication);
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }
}
