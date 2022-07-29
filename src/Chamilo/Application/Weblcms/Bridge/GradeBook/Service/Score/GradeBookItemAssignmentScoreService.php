<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\AssignmentEntity\AssignmentScoreServiceManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\Repository\PublicationRepository as AssignmentPublicationRepository;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookItemAssignmentScoreService implements GradeBookItemScoreServiceInterface
{
    /**
     * @var AssignmentPublicationRepository
     */
    protected $assignmentPublicationRepository;

    /**
     * @var AssignmentScoreServiceManager
     */
    protected $scoreServiceManager;

    /**
     * @param AssignmentPublicationRepository $assignmentPublicationRepository
     * @param AssignmentScoreServiceManager $scoreServiceManager
     */
    public function __construct(AssignmentPublicationRepository $assignmentPublicationRepository, AssignmentScoreServiceManager $scoreServiceManager)
    {
        $this->assignmentPublicationRepository = $assignmentPublicationRepository;
        $this->scoreServiceManager = $scoreServiceManager;
    }

    /**
     * @param ContentObjectPublication $publication
     * @param array $userIds
     *
     * @return array
     */
    public function getScores(ContentObjectPublication $publication, array $userIds): array
    {
        $assignmentPublication = $this->assignmentPublicationRepository->findPublicationByContentObjectPublication($publication);
        $scoreService = $this->scoreServiceManager->getScoreServiceByType($assignmentPublication->getEntityType());
        return $scoreService->getScores($publication, $userIds);
    }
}