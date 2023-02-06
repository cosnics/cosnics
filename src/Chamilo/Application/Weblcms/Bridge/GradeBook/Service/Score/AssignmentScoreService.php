<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\EntityDataService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository as AssignmentPublicationRepository;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScore;
use Chamilo\Core\Repository\ContentObject\GradeBook\Domain\GradeScoreInterface;
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
     * @var EntityDataService
     */
    protected $entityDataService;

    /**
     * @param AssignmentPublicationRepository $assignmentPublicationRepository
     * @param AssignmentService $assignmentService
     * @param EntityDataService $entityDataService
     */
    public function __construct(AssignmentPublicationRepository $assignmentPublicationRepository, AssignmentService $assignmentService, EntityDataService $entityDataService)
    {
        $this->assignmentPublicationRepository = $assignmentPublicationRepository;
        $this->assignmentService = $assignmentService;
        $this->entityDataService = $entityDataService;
    }

    /**
     * @param ContentObjectPublication $publication
     *
     * @return GradeScoreInterface[]
     */
    public function getScores(ContentObjectPublication $publication): array
    {
        $entityType = $this->getEntityTypeFromPublication($publication);
        $entityScores = $this->assignmentService->getMaxScoresForContentObjectPublicationEntityType($publication, $entityType);

        /** @var GradeScore[] $scores */
        $scores = array();
        switch ($entityType)
        {
            case 0:
                foreach ($entityScores as $entityScore)
                {
                    $entityId = $entityScore['entity_id'];
                    $scores[$entityId] = new GradeScore((float) $entityScore['maximum_score']);
                }
                return $scores;
            case 1:
                $userEntities = $this->entityDataService->getCourseGroupUserEntitiesRecursiveFromCourse($publication->get_course_id());
                break;
            case 2:
                $userEntities = $this->getPlatformGroupUserEntitiesFromEntityScores($entityScores);
                break;
        }

        foreach ($entityScores as $entityScore)
        {
            $gradeScore = new GradeScore((float) $entityScore['maximum_score']);
            $entityId = $entityScore['entity_id'];
            $users = $userEntities[$entityId];

            foreach ($users as $userId)
            {
                $hasKey = array_key_exists($userId, $scores);
                if (!$hasKey || $gradeScore->hasPresedenceOver($scores[$userId]))
                {
                    $scores[$userId] = $gradeScore;
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
            $userEntities[$entityId] = $this->entityDataService->getUserEntitiesFromPlatformGroup($entityId);
        }
        return $userEntities;
    }
}