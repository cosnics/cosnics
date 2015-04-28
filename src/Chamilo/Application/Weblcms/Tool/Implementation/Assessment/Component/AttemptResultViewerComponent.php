<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataClass\Publication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
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
use Chamilo\Libraries\Format\Structure\Page;

/**
 * This class displays the result of a single attempt
 *
 * @package application.weblcms.tool.assessmet
 * @author Sven Vanpoucke
 */
class AttemptResultViewerComponent extends Manager
{
    const PARAM_SHOW_FULL = 'show_full';

    /**
     * The assessment object
     *
     * @var Assessment
     */
    private $assessment;

    /**
     * The assessment attempt
     *
     * @var AssessmentAttempt
     */
    private $assessment_attempt;

    /**
     * The content object publication
     *
     * @var ContentObjectPublication
     */
    private $assessment_publication;

    /**
     * Runs this component
     *
     * @throws \libraries\architecture\exceptions\NotAllowedException
     */
    public function run()
    {
        $assessment_attempt_id = Request :: get(self :: PARAM_USER_ASSESSMENT);

        $condition = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt :: class_name(), AssessmentAttempt :: PROPERTY_ID),
            new StaticConditionVariable($assessment_attempt_id));

        $this->assessment_attempt = DataManager :: retrieve(
            AssessmentAttempt :: class_name(),
            new DataClassRetrieveParameters($condition));

        if (! $this->assessment_attempt)
        {
            $this->redirect(
                Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES),
                true,
                array(),
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION,
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID));
        }

        $this->assessment_publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $this->assessment_attempt->get_assessment_id());

        $parameters = new DataClassRetrieveParameters(
            new EqualityCondition(
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($this->assessment_publication->get_id())));
        $assessment_publication = DataManager :: retrieve(Publication :: class_name(), $parameters);

        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT, $this->assessment_publication))
        {
            throw new NotAllowedException();
        }
        elseif (! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            if ($this->get_user_id() != $this->assessment_attempt->get_user_id())
            {
                throw new NotAllowedException();
            }
            elseif ($this->assessment_attempt->get_status() == AssessmentAttempt :: STATUS_NOT_COMPLETED)
            {
                throw new NotAllowedException();
            }
            elseif (! $assessment_publication->get_configuration()->show_feedback())
            {
                throw new NotAllowedException();
            }
        }

        $assessment = $this->assessment_publication->get_content_object();
        $this->assessment = $assessment;

        $this->add_assessment_title_breadcrumb($assessment);

        Request :: set_get(\Chamilo\Core\Repository\Display\Manager :: PARAM_ACTION, self :: ACTION_VIEW_RESULTS);

        $context = ClassnameUtilities :: getInstance()->getNamespaceFromClassname($assessment->get_type()) . '\display';
        $factory = new ApplicationFactory($this->getRequest(), $context, $this->get_user(), $this);
        return $factory->run();
    }

    /**
     * Displays the header, depending on the parameters
     */
    public function render_header()
    {
        $html = array();

        if (! Request :: get(self :: PARAM_SHOW_FULL))
        {
            Page :: getInstance()->setViewMode(Page :: VIEW_MODE_HEADERLESS);
        }

        $html[] = parent :: render_header();

        if ($this->assessment_attempt->get_status() == AssessmentAttempt :: STATUS_NOT_COMPLETED)
        {
            $html[] = '<div class="warning-message">' . Translation :: get('AttemptNotCompleted') . '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Retrieves the assessment results
     *
     * @return array
     */
    public function retrieve_assessment_results()
    {
        $question_attempt_id = Request :: get(self :: PARAM_QUESTION_ATTEMPT);
        if ($question_attempt_id)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(QuestionAttempt :: class_name(), QuestionAttempt :: PROPERTY_ID),
                new StaticConditionVariable($question_attempt_id));
        }
        else
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    QuestionAttempt :: class_name(),
                    QuestionAttempt :: PROPERTY_ASSESSMENT_ATTEMPT_ID),
                new StaticConditionVariable($this->assessment_attempt->get_id()));
        }

        $question_attempts = DataManager :: retrieves(
            QuestionAttempt :: class_name(),
            new DataClassRetrievesParameters($condition));

        $results = array();

        while ($question_attempt = $question_attempts->next_result())
        {
            $results[$question_attempt->get_question_complex_id()] = array(
                'answer' => $question_attempt->get_answer(),
                'feedback' => $question_attempt->get_feedback(),
                'score' => $question_attempt->get_score(),
                'hint' => $question_attempt->get_hint());
        }

        return $results;
    }

    /**
     * Changes the answer data
     *
     * @param int $question_cid
     * @param int $score
     * @param string $feedback
     */
    public function change_answer_data($question_cid, $score, $feedback)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                QuestionAttempt :: class_name(),
                QuestionAttempt :: PROPERTY_ASSESSMENT_ATTEMPT_ID),
            new StaticConditionVariable($this->assessment_attempt->get_id()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                QuestionAttempt :: class_name(),
                QuestionAttempt :: PROPERTY_QUESTION_COMPLEX_ID),
            new StaticConditionVariable($question_cid));

        $condition = new AndCondition($conditions);

        $question_attempt = DataManager :: retrieve(
            QuestionAttempt :: class_name(),
            new DataClassRetrieveParameters($condition));

        $question_attempt->set_score($score);
        $question_attempt->set_feedback($feedback);
        $question_attempt->update();
    }

    /**
     * Changes the total score
     *
     * @param int $total_score
     */
    public function change_total_score($total_score)
    {
        $this->assessment_attempt->set_total_score($total_score);
        $this->assessment_attempt->update();
    }

    /**
     * Returns whether or not the answer data can be changed
     *
     * @return bool
     */
    public function can_change_answer_data()
    {
        if (Request :: get(self :: PARAM_SHOW_FULL))
        {
            return $this->assessment_attempt->get_status() == AssessmentAttempt :: STATUS_COMPLETED &&
                 $this->is_allowed(WeblcmsRights :: EDIT_RIGHT);
        }

        return false;
    }

    /**
     * Returns the root content object
     *
     * @return Assessment
     */
    public function get_root_content_object()
    {
        return $this->assessment;
    }

    /**
     * Returns the assessment parameters
     *
     * @return array
     */
    public function get_assessment_parameters()
    {
        return array();
    }

    /**
     * Returns the parameters for automatic registration
     *
     * @return array
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_ASSESSMENT, self :: PARAM_USER_ASSESSMENT, self :: PARAM_SHOW_FULL);
    }

    /**
     * Add a breadcrumb with the title of the assessment
     *
     * @param $assessment Assessment
     */
    protected function add_assessment_title_breadcrumb($assessment)
    {
        $breadcrumb_trail = BreadcrumbTrail :: get_instance();
        $breadcrumbs = $breadcrumb_trail->get_breadcrumbs();

        $breadcrumbs[$breadcrumb_trail->size() - 1] = new Breadcrumb(
            $this->get_url(
                array(self :: PARAM_ACTION => self :: ACTION_VIEW_RESULTS),
                array(self :: PARAM_USER_ASSESSMENT, self :: PARAM_SHOW_FULL)),
            Translation :: get('ViewResultsForAssessment', array('TITLE' => $assessment->get_title())));

        $breadcrumb_trail->set_breadcrumbtrail($breadcrumbs);

        $user_fullname = \Chamilo\Core\User\Storage\DataManager :: get_fullname_from_user(
            $this->assessment_attempt->get_user_id());

        $breadcrumb_trail->add(
            new Breadcrumb($this->get_url(), Translation :: get('ViewResultsForUser', array('USER' => $user_fullname))));
    }

    public function get_assessment_configuration()
    {
        $parameters = new DataClassRetrieveParameters(
            new EqualityCondition(
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_PUBLICATION_ID),
                new StaticConditionVariable($this->assessment_publication->get_id())));
        $assessment_publication = DataManager :: retrieve(Publication :: class_name(), $parameters);

        return $assessment_publication->get_configuration();
    }
}
