<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Component;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeAttempt;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathTreeNodeQuestionAttempt;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Manager;
use Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\AssessmentResult;
use Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\AssessmentResultsExportController;
use Chamilo\Core\Repository\ContentObject\Assessment\ResultsExporter\QuestionResult;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\TrackingRepository;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

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
    protected $courseGroups = [];

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
        $tree = $this->getLearningPathService()->getTree($learningPath);

        $assessments = array();

        foreach ($tree->getTreeNodes() as $treeNode)
        {
            if ($treeNode->getContentObject() instanceof Assessment)
            {
                $assessments[] = $treeNode->getContentObject();
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
        $learningPathTrackingRepository = new TrackingRepository(
            $this->getDataClassRepository(),
            new TrackingParameters((int) $this->get_publication_id())
        );

        $learningPathAttempts = $learningPathTrackingRepository
            ->findLearningPathAttemptsWithTreeNodeAttemptsAndTreeNodeQuestionAttempts($learningPath);

        $assessment_results = array();

        foreach ($learningPathAttempts as $learningPathAttempt)
        {
            $treeNodeAttemptId = $learningPathAttempt['tree_node_data_attempt_id'];
            if (array_key_exists($treeNodeAttemptId, $learningPathAttempt))
            {
                $assessment_result = $assessment_results[$treeNodeAttemptId];
            }
            else
            {
                $assessment_result = new AssessmentResult(
                    $treeNodeAttemptId,
                    $learningPathAttempt[LearningPathTreeNodeAttempt::PROPERTY_LEARNING_PATH_ID],
                    null,
                    array(),
                    $learningPathAttempt[LearningPathTreeNodeAttempt::PROPERTY_START_TIME],
                    $learningPathAttempt[LearningPathTreeNodeAttempt::PROPERTY_SCORE],
                    $learningPathAttempt[LearningPathTreeNodeAttempt::PROPERTY_TOTAL_TIME],
                    $learningPathAttempt[LearningPathTreeNodeAttempt::PROPERTY_USER_ID]
                );

                $assessment_results[] = $assessment_result;
            }

            $assessment_result->addQuestionResult(
                new QuestionResult(
                    unserialize($learningPathAttempt[LearningPathTreeNodeQuestionAttempt::PROPERTY_ANSWER]),
                    $assessment_result,
                    $learningPathAttempt[LearningPathTreeNodeQuestionAttempt::PROPERTY_QUESTION_COMPLEX_ID],
                    $learningPathAttempt[LearningPathTreeNodeQuestionAttempt::PROPERTY_SCORE],
                    array(
                        self::COLUMN_COURSE_GROUP_ID => $this->getCourseGroupsForUser(
                            $learningPathAttempt[LearningPathTreeNodeAttempt::PROPERTY_USER_ID]
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
