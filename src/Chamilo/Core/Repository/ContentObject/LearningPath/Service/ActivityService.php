<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\Repository\ActivityRepository;
use Chamilo\Libraries\Storage\Query\OrderProperty;

/**
 * Service to manage activities of learning path tree nodes
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ActivityService
{
    /**
     * @var ActivityRepository
     */
    protected $activityRepository;

    /**
     * @var AutomaticNumberingService
     */
    protected $automaticNumberingService;

    /**
     * ActivityService constructor.
     *
     * @param ActivityRepository $activityRepository
     * @param AutomaticNumberingService $automaticNumberingService
     */
    public function __construct(
        ActivityRepository $activityRepository, AutomaticNumberingService $automaticNumberingService
    )
    {
        $this->activityRepository = $activityRepository;
        $this->automaticNumberingService = $automaticNumberingService;
    }

    /**
     * Counts the activities for a given tree node
     *
     * @param TreeNode $treeNode
     *
     * @return int
     */
    public function countActivitiesForTreeNode(TreeNode $treeNode)
    {
        $activitiesCount = 0;

        /** @var TreeNode[] $treeNodes */
        $treeNodes = array_merge([$treeNode], $treeNode->getDescendantNodes());

        foreach ($treeNodes as $treeNode)
        {
            $activitiesCount += $this->activityRepository->countActivitiesForContentObject(
                $treeNode->getContentObject()
            );
        }

        return $activitiesCount;
    }

    /**
     * Retrieves the activities for a given tree node
     *
     * @param TreeNode $treeNode
     * @param int $offset
     * @param int $count
     * @param OrderProperty|null $orderBy
     *
     * @return \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity[]
     */
    public function retrieveActivitiesForTreeNode(
        TreeNode $treeNode, $offset = null, $count = null, OrderProperty $orderBy = null
    )
    {
        $activities = [];

        /** @var TreeNode[] $treeNodes */
        $treeNodes = array_merge([$treeNode], $treeNode->getDescendantNodes());

        foreach ($treeNodes as $treeNode)
        {
            $contentObjectActivities = $this->activityRepository->retrieveActivitiesForContentObject(
                $treeNode->getContentObject()
            );

            $pathTitles = [];

            foreach ($treeNode->getParentNodes() as $parentTreeNode)
            {
                $pathTitles[] = $this->automaticNumberingService->getAutomaticNumberedTitleForTreeNode($parentTreeNode);
            }

            $pathTitles[] = $this->automaticNumberingService->getAutomaticNumberedTitleForTreeNode($treeNode);

            foreach ($contentObjectActivities as $activity)
            {
                $activityInstance = clone $activity;
                $activityInstance->set_content(implode(' > ', $pathTitles));
                $activities[] = $activityInstance;
            }
        }

        return $this->activityRepository->filterActivities($activities, $offset, $count, $orderBy);
    }
}
