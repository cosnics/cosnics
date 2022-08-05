<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service;

use Chamilo\Application\Weblcms\Service\PublicationService;
use Chamilo\Core\Repository\ContentObject\GradeBook\Storage\Entity\GradeBookItem;
use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score\ScoreServiceManager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPathStepContext;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\LearningPathStepContextRepository;

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
     * @var ScoreServiceManager
     */
    protected $scoreServiceManager;

    /**
     * @var LearningPathService
     */
    protected $learningPathService;

    /**
     * @var LearningPathStepContextRepository
     */
    protected $learningPathStepContextRepository;

    /**
     * @param PublicationService $publicationService
     * @param ScoreServiceManager $scoreServiceManager
     * @param LearningPathService $learningPathService
     * @param LearningPathStepContextRepository $learningPathStepContextRepository
     */
    public function __construct(PublicationService $publicationService, ScoreServiceManager $scoreServiceManager, LearningPathService $learningPathService, LearningPathStepContextRepository $learningPathStepContextRepository)
    {
        $this->publicationService = $publicationService;
        $this->scoreServiceManager = $scoreServiceManager;
        $this->learningPathService = $learningPathService;
        $this->learningPathStepContextRepository = $learningPathStepContextRepository;
    }

    /**
     * @param GradeBookItem $gradeBookItem
     * @param int[] $userIds
     *
     * @return array
     * @throws \Exception
     */
    public function getScores(GradeBookItem $gradeBookItem, array $userIds): array
    {
        $contextClass = $gradeBookItem->getContextClass();
        $contextId = $gradeBookItem->getContextId();

        if ($gradeBookItem->getContextClass() == LearningPathStepContext::class)
        {
            $userScores = $this->getTreeNodeScores($gradeBookItem);
            $tool = 'LearningPath';
        }
        else
        {
            $publication = $this->publicationService->getPublication($contextId);
            $tool = $publication->get_tool();
            $scoreService = $this->scoreServiceManager->getScoreServiceByType($tool);
            $userScores = $scoreService->getScores($publication);
        }

        $scores = array();
        foreach ($userIds as $userId)
        {
            $scores[] = ['user_id' => (int) $userId, 'score' => $userScores[$userId]];
        }
        return ['id' => $gradeBookItem->getId(), 'context_class' => $contextClass, 'context_id' => $contextId, 'tool' => $tool, 'scores' => $scores];
    }

    /**
     * @param GradeBookItem $gradeBookItem
     *
     * @return array
     * @throws \Exception
     */
    protected function getTreeNodeScores(GradeBookItem $gradeBookItem): array
    {
        $lpsContext = $this->learningPathStepContextRepository->findLearningPathStepContextById($gradeBookItem->getContextId());
        $publication = $this->publicationService->getPublication($lpsContext->getContextId());
        $learningPath = $publication->getContentObject();
        if (!$learningPath instanceof LearningPath)
        {
            throw new \Exception('Content object ' . $learningPath->getId() . ' is not a learning path.');
        }
        $tree = $this->learningPathService->getTree($learningPath);
        $treeNode = $tree->getTreeNodeById($lpsContext->getLearningPathStepId());
        $scoreServiceType = get_class($treeNode->getContentObject())::class_name(false);
        $scoreService = $this->scoreServiceManager->getLearningPathScoreServiceByType($scoreServiceType);
        return $scoreService->getScoresFromTreeNode($publication, $treeNode);
    }
}