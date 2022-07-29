<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service;

use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\GradeBookItemScoreServiceManager;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service
 *
 * @author Stefan Gabriels - Hogeschool Gent
 */
class GradeBookItemScoreService
{
    /**
     * @var PublicationService
     */
    protected $publicationService;

    /**
     * @var GradeBookItemScoreServiceManager
     */
    protected $scoreServiceManager;

    /**
     * @param PublicationService $publicationService
     * @param GradeBookItemScoreServiceManager $scoreServiceManager
     */
    public function __construct(PublicationService $publicationService, GradeBookItemScoreServiceManager $scoreServiceManager)
    {
        $this->publicationService = $publicationService;
        $this->scoreServiceManager = $scoreServiceManager;
    }

    /**
     * @param GradeBookItem $gradeBookItem
     * @param int[] $userIds
     *
     * @return array
     */
    public function getScores(GradeBookItem $gradeBookItem, array $userIds): array
    {
        $publication = $this->publicationService->getPublication($gradeBookItem->getContextId());
        $scoreService = $this->scoreServiceManager->getScoreServiceByType($publication->get_tool());
        $scores = $scoreService->getScores($publication, $userIds);

        return ['id' => $gradeBookItem->getId(), 'context_class' => $gradeBookItem->getContextClass(), 'context_id' => $gradeBookItem->getContextId(), 'tool' => $publication->get_tool(), 'scores' => $scores];
    }
}