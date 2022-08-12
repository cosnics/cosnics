<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\ScoreDataService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository as AssignmentPublicationRepository;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Repository\CourseGroupRepository;
use Chamilo\Libraries\Storage\Iterator\DataClassIterator;
use Chamilo\Libraries\Storage\Iterator\RecordIterator;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class AssignmentScoreService implements ScoreServiceInterface
{
    /**
     * @var AssignmentPublicationRepository
     */
    protected $assignmentPublicationRepository;

    /**
     * @var AssignmentService
     */
    protected $assignmentService;

    /**
     * @var CourseGroupRepository
     */
    protected $courseGroupRepository;

    /**
     * @var ScoreDataService
     */
    protected $scoreDataService;

    /**
     * @param AssignmentPublicationRepository $assignmentPublicationRepository
     * @param AssignmentService $assignmentService
     * @param CourseGroupRepository $courseGroupRepository
     * @param ScoreDataService $scoreDataService
     */
    public function __construct(AssignmentPublicationRepository $assignmentPublicationRepository, AssignmentService $assignmentService, CourseGroupRepository $courseGroupRepository, ScoreDataService $scoreDataService)
    {
        $this->assignmentPublicationRepository = $assignmentPublicationRepository;
        $this->assignmentService = $assignmentService;
        $this->courseGroupRepository = $courseGroupRepository;
        $this->scoreDataService = $scoreDataService;
    }

    /**
     * @param ContentObjectPublication $publication
     *
     * @return array
     */
    public function getScores(ContentObjectPublication $publication): array
    {
        $entityType = $this->getEntityTypeFromPublication($publication);
        $entityScores = $this->assignmentService->getMaxScoresForContentObjectPublicationEntityType($publication, $entityType);

        if ($entityType == 1)
        {
            $userEntities = $this->getCourseGroupUserEntitiesRecursiveFromPublication($publication);
        }
        else if ($entityType == 2)
        {
            $userEntities = $this->getPlatformGroupUserEntitiesFromEntityScores($entityScores);
        }


        $scores = array();
        foreach ($entityScores as $entityScore)
        {
            $maxScore = (float) $entityScore['maximum_score'];
            $entityId = $entityScore['entity_id'];

            if ($entityType == 0)
            {
                $scores[$entityId] = $maxScore;
            }
            else
            {
                $users = $userEntities[$entityId];

                foreach ($users as $userId)
                {
                    $hasKey = array_key_exists($userId, $scores);
                    if (!$hasKey || ($maxScore > $scores[$userId]))
                    {
                        $scores[$userId] = $maxScore;
                    }
                }
            }
        }
        return $scores;
    }

    /**
     * @param ContentObjectPublication $publication
     *
     * @return int
     */
    protected function getEntityTypeFromPublication(ContentObjectPublication $publication): int
    {
        if ($publication->get_tool() == 'ExamAssignment')
        {
            return 0;
        }

        $assignmentPublication = $this->assignmentPublicationRepository->findPublicationByContentObjectPublication($publication);
        return $assignmentPublication->getEntityType();
    }

    /**
     * @param ContentObjectPublication $publication
     *
     * @return array
     */
    protected function getCourseGroupUserEntitiesRecursiveFromPublication(ContentObjectPublication $publication): array
    {
        $courseGroups = $this->toAssociativeArray(
            $this->courseGroupRepository->getCourseGroupsInCourse($publication->get_course_id())
        );

        $courseGroupIdsRecursive = [];
        foreach ($courseGroups as $courseGroup)
        {
            $courseGroupId = $courseGroup->getId();
            $groupIdAndAncestorIds = [$courseGroupId];
            $curCourseGroup = $courseGroup;
            while ($curCourseGroup->get_parent_id() != 0)
            {
                $parentId = $curCourseGroup->get_parent_id();
                $groupIdAndAncestorIds[] = $parentId;
                $curCourseGroup = $courseGroups[$parentId];
            }
            $courseGroupIdsRecursive[$courseGroupId] = $groupIdAndAncestorIds;
        }

        return $this->scoreDataService->getUserEntitiesFromCourseGroupsRecursive(array_keys($courseGroups), $courseGroupIdsRecursive);
    }

    /**
     * @param RecordIterator $entityScores
     *
     * @return array
     */
    protected function getPlatformGroupUserEntitiesFromEntityScores(RecordIterator $entityScores): array
    {
        $userEntities = [];
        foreach ($entityScores as $entityScore)
        {
            $entityId = $entityScore['entity_id'];
            $userEntities[$entityId] = $this->scoreDataService->getUserEntitiesFromPlatformGroup($entityId);
        }
        return $userEntities;
    }

    /**
     * @param DataClassIterator $courseGroups
     *
     * @return array
     */
    protected function toAssociativeArray(DataClassIterator $courseGroups): array
    {
        $groups = [];
        foreach ($courseGroups as $courseGroup)
        {
            $courseGroupId = $courseGroup->getId();
            $groups[$courseGroupId] = $courseGroup;
        }
        return $groups;
    }
}