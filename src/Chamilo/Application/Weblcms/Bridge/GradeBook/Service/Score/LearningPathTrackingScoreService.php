<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Bridge\GradeBook\Service\TrackingServiceBuilderService;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathTrackingScoreService implements LearningPathScoreServiceInterface
{
    /**
     * @param TrackingServiceBuilderService
     */
    protected $trackingServiceBuilderService;

    /**
     * @param TrackingServiceBuilderService $trackingServiceBuilderService
     */
    public function __construct(TrackingServiceBuilderService $trackingServiceBuilderService)
    {
        $this->trackingServiceBuilderService = $trackingServiceBuilderService;
    }

    /**
     * @param ContentObjectPublication $publication
     * @param TreeNode $treeNode
     *
     * @return array
     * @throws \Exception
     */
    public function getScoresFromTreeNode(ContentObjectPublication $publication, TreeNode $treeNode): array
    {
        $learningPath = $publication->getContentObject();

        if (!$learningPath instanceof LearningPath)
        {
            throw new \Exception('Content object ' . $learningPath->getId() . ' is not a learning path.');
        }
        $trackingService = $this->trackingServiceBuilderService->buildTrackingServiceForPublication($publication);
        $learningPathAttempts = $trackingService->getLearningPathAttemptsWithUser($learningPath, $treeNode);

        $scores = array();
        foreach ($learningPathAttempts as $attempt)
        {
            $userId = $attempt['user_id'];
            $scores[$userId] = (float) $attempt['max_score'];
        }
        return $scores;
    }
}
