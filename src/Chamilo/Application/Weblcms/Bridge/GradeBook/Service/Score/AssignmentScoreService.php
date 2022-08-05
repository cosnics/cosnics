<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository as AssignmentPublicationRepository;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\Entity\EntityServiceManager as PublicationEntityServiceManager;
use Chamilo\Application\Weblcms\Bridge\Assignment\Service\AssignmentService;

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
     * @var PublicationEntityServiceManager
     */
    protected $publicationEntityServiceManager;

    /**
     * @var AssignmentService
     */
    protected $assignmentService;

    /**
     * @param AssignmentPublicationRepository $assignmentPublicationRepository
     * @param PublicationEntityServiceManager $publicationEntityServiceManager
     * @param AssignmentService $assignmentService
     */
    public function __construct(AssignmentPublicationRepository $assignmentPublicationRepository, PublicationEntityServiceManager $publicationEntityServiceManager, AssignmentService $assignmentService)
    {
        $this->assignmentPublicationRepository = $assignmentPublicationRepository;
        $this->publicationEntityServiceManager = $publicationEntityServiceManager;
        $this->assignmentService = $assignmentService;
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
        $entityService = $this->publicationEntityServiceManager->getEntityServiceByType($entityType);

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
                $users = $entityService->getUsersForEntity($entityId);
                foreach ($users as $user)
                {
                    $userId = $user->getId();
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
}