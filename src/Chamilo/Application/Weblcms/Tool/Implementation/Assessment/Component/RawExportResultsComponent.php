<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\QuestionAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assessment\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\AssessmentResult;
use Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\AssessmentResultsExportController;
use Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\QuestionResult;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Description of raw_export_results
 *
 * @author Anthony Hurst (Hogeschool Gent)
 * @author Sven Vanpoucke - Hogeschool Gent (Refactoring to abstract exporter in assessment content object)
 */
class RawExportResultsComponent extends Manager
{
    public const COLUMN_COURSE_GROUP_ID = 'course_group_id';

    /**
     * Runs this component
     *
     * @throws \libraries\architecture\exceptions\NotAllowedException
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $additional_information_columns = [
            self::COLUMN_COURSE_GROUP_ID => Translation::get(
                'CourseGroups', null, \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace('course_group')
            )
        ];

        $controller = new AssessmentResultsExportController(
            $this->get_assessments(), $this->get_assessment_results(), $additional_information_columns
        );

        $path = $controller->run();

        $this->getFilesystemTools()->sendFileForDownload($path);
        $this->getFilesystem()->remove($path);
    }

    /**
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $this->addBrowserBreadcrumb($breadcrumbtrail);
    }

    /**
     * Retrieves the assessment attempts and places them in AssessmentResult classes
     *
     * @return AssessmentResult[]
     */
    protected function get_assessment_results()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(AssessmentAttempt::class, AssessmentAttempt::PROPERTY_ASSESSMENT_ID),
            new StaticConditionVariable($this->get_publication_id())
        );

        $assessment_results = [];

        $assessment_attempts = WeblcmsTrackingDataManager::retrieves(
            AssessmentAttempt::class, new DataClassRetrievesParameters($condition)
        );
        foreach ($assessment_attempts as $assessment_attempt)
        {
            $assessment_result = new AssessmentResult(
                $assessment_attempt->get_id(), $assessment_attempt->get_assessment_id(),
                $assessment_attempt->get_end_time(), [], $assessment_attempt->get_start_time(),
                $assessment_attempt->get_total_score(), $assessment_attempt->get_total_time(),
                $assessment_attempt->get_user_id()
            );

            $assessment_result->set_question_results(
                $this->get_question_results_from_assessment_attempt($assessment_attempt, $assessment_result)
            );

            $assessment_results[] = $assessment_result;
        }

        return $assessment_results;
    }

    /**
     * Retrieves the assessment objects and put them in an array
     *
     * @return Assessment[]
     */
    protected function get_assessments()
    {
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class, $this->get_publication_id()
        );

        return [$publication->get_content_object()];
    }

    /**
     * Returns the publication id
     *
     * @return int
     */
    protected function get_publication_id()
    {
        return Request::get(self::PARAM_ASSESSMENT);
    }

    /**
     * Retrieves the question attempts and places them in the QuestionResult classes
     *
     * @param AssessmentAttempt $assessment_attempt
     * @param AssessmentResult $assessment_result
     *
     * @return QuestionAttempt[]
     */
    protected function get_question_results_from_assessment_attempt($assessment_attempt, $assessment_result)
    {
        $course_groups =
            \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::get_course_groups_from_user_as_string(
                $assessment_attempt->get_user_id(), $this->get_course_id()
            );

        $additional_information = [self::COLUMN_COURSE_GROUP_ID => $course_groups];

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                QuestionAttempt::class, QuestionAttempt::PROPERTY_ASSESSMENT_ATTEMPT_ID
            ), new StaticConditionVariable($assessment_attempt->get_id())
        );

        $question_results = [];

        $question_attempts = DataManager::retrieves(
            QuestionAttempt::class, new DataClassRetrievesParameters($condition)
        );
        foreach ($question_attempts as $question_attempt)
        {
            $question_results[] = new QuestionResult(
                unserialize($question_attempt->get_answer()), $assessment_result,
                $question_attempt->get_question_complex_id(), $question_attempt->get_score(), $additional_information
            );
        }

        return $question_results;
    }
}
