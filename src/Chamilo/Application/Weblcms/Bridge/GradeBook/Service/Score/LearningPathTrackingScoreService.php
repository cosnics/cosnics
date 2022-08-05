<?php
namespace Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingServiceBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;

/**
 * @package Chamilo\Application\Weblcms\Bridge\GradeBook\Service\Score
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class LearningPathTrackingScoreService implements LearningPathScoreServiceInterface
{

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
        $trackingServiceBuilder = $this->getTrackingServiceBuilder();
        $trackingService = $trackingServiceBuilder->buildTrackingService(new TrackingParameters((int) $publication->getId()));
        $learningPathAttempts = $trackingService->getLearningPathAttemptsWithUser($learningPath, $treeNode);

        $scores = array();
        foreach ($learningPathAttempts as $attempt)
        {
            $userId = $attempt['user_id'];
            $scores[$userId] = (float) $attempt['max_score'];
        }
        return $scores;
    }

    /**
     * @return TrackingServiceBuilder
     * @throws \Exception
     */
    protected function getTrackingServiceBuilder()
    {
        $container = DependencyInjectionContainerBuilder::getInstance()->createContainer();
        /** @var DataClassRepository */
        $dataClassRepository = $container->get('chamilo.libraries.storage.data_manager.doctrine.data_class_repository');
        return new TrackingServiceBuilder($dataClassRepository);
    }
}
