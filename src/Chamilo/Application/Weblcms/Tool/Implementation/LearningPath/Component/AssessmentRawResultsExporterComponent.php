<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathChildAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathQuestionAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\LearningPathTrackingParameters;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\AssessmentResult;
use Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\AssessmentResultsExportController;
use Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\QuestionResult;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\ComplexLearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathTrackingRepository;
use Chamilo\Core\Repository\ContentObject\LearningPathItem\Storage\DataClass\ComplexLearningPathItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Exports the raw results of all the assessments of a learning path
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AssessmentRawResultsExporterComponent extends Manager
{
    const COLUMN_COURSE_GROUP_ID = 'course_group_id';

    /**
     * CourseGroups cache per user
     *
     * @var string[]
     */
    protected $courseGroups;

    /**
     * Runs this component
     */
    public function run()
    {
        ini_set('memory_limit', - 1);

        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $additional_information_columns = array(
            self::COLUMN_COURSE_GROUP_ID => Translation::get(
                'CourseGroups',
                null,
                \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace('course_group')
            )
        );

        $contentObjectPublicationId = $this->get_publication_id();

        /** @var ContentObjectPublication $publication */
        $publication = WeblcmsDataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $contentObjectPublicationId
        );

        if (!$publication instanceof ContentObjectPublication)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation('ContentObjectPublication'), $contentObjectPublicationId
            );
        }

        /** @var LearningPath $learningPath */
        $learningPath = $publication->getContentObject();

        $controller = new AssessmentResultsExportController(
            $this->getAssessmentsFromLearningPath($learningPath),
            $this->getAssessmentResultsForLearningPath($learningPath),
            $additional_information_columns
        );

        $path = $controller->run();

        Filesystem::file_send_for_download($path, true);
        Filesystem::remove($path);
    }

    /**
     * Recursive function to retrieve all the assessments from a learning path and his children
     *
     * @param LearningPath $learningPath
     *
     * @return Assessment[]
     */
    protected function getAssessmentsFromLearningPath(LearningPath $learningPath)
    {
        $learningPathTreeBuilder = $this->getLearningPathTreeBuilder();
        $learningPathTree = $learningPathTreeBuilder->buildLearningPathTree($learningPath);

        $assessments = array();

        foreach ($learningPathTree->getLearningPathTreeNodes() as $learningPathTreeNode)
        {
            if ($learningPathTreeNode->getContentObject() instanceof Assessment)
            {
                $assessments[] = $learningPathTreeNode->getContentObject();
            }
        }

        return $assessments;
    }

    /**
     * Returns the assessment results
     *
     * @param LearningPath $learningPath
     *
     * @return AssessmentResult[]
     */
    protected function getAssessmentResultsForLearningPath(LearningPath $learningPath)
    {
        $learningPathTrackingRepository = new LearningPathTrackingRepository(
            $this->getDataClassRepository(),
            new LearningPathTrackingParameters((int) $this->get_course_id(), (int) $this->get_publication_id())
        );

        $learningPathAttempts = $learningPathTrackingRepository
            ->findLearningPathAttemptsWithLearningPathChildAttemptsAndLearningPathQuestionAttempts($learningPath);

        $assessment_results = array();

        foreach ($learningPathAttempts as $learningPathAttempt)
        {
            $learningPathChildAttemptId = $learningPathAttempt['learning_path_child_attempt_id'];
            if (array_key_exists($learningPathChildAttemptId, $learningPathAttempt))
            {
                $assessment_result = $assessment_results[$learningPathChildAttemptId];
            }
            else
            {
                $assessment_result = new AssessmentResult(
                    $learningPathChildAttemptId,
                    $learningPathAttempt[LearningPathAttempt::PROPERTY_LEARNING_PATH_ID],
                    null,
                    array(),
                    $learningPathAttempt[LearningPathChildAttempt::PROPERTY_START_TIME],
                    $learningPathAttempt[LearningPathChildAttempt::PROPERTY_SCORE],
                    $learningPathAttempt[LearningPathChildAttempt::PROPERTY_TOTAL_TIME],
                    $learningPathAttempt[LearningPathAttempt::PROPERTY_USER_ID]
                );

                $assessment_results[] = $assessment_result;
            }

            $assessment_result->addQuestionResult(
                new QuestionResult(
                    unserialize($learningPathAttempt[LearningPathQuestionAttempt::PROPERTY_ANSWER]),
                    $assessment_result,
                    $learningPathAttempt[LearningPathQuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID],
                    $learningPathAttempt[LearningPathQuestionAttempt::PROPERTY_SCORE],
                    array(
                        self::COLUMN_COURSE_GROUP_ID => $this->getCourseGroupsForUser(
                            $learningPathAttempt[LearningPathAttempt::PROPERTY_USER_ID]
                        )
                    )
                )
            );
        }

        return $assessment_results;
    }

    /**
     * Returns the course groups for a given user
     *
     * @param $userId
     *
     * @return string
     */
    protected function getCourseGroupsForUser($userId)
    {
        if (!array_key_exists($userId, $this->courseGroups))
        {
            $this->courseGroups[$userId] =
                \Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager::get_course_groups_from_user_as_string(
                    $userId, $this->get_course_id()
                );
        }

        return $this->courseGroups[$userId];
    }

    /**
     * Returns the publication id
     *
     * @return int
     */
    protected function get_publication_id()
    {
        return Request::get(self::PARAM_PUBLICATION_ID);
    }
}
