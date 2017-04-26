<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns the learning path tree nodes as JSON
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetLearningPathTreeNodesComponent extends Manager
{
    /**
     * Executes this component and returns its output
     */
    public function run()
    {
        try
        {
            $learningPathTree = $this->get_application()->getLearningPathTree();
            $treeData = array($this->getTreeNodesAsArray($learningPathTree->getRoot()));

            return new JsonResponse($treeData);
        }
        catch (\Exception $ex)
        {
            JsonAjaxResult::general_error();
        }
    }

    /**
     * Builds the data from a LearningPathTreeNode as an array
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return array
     */
    protected function getTreeNodesAsArray(LearningPathTreeNode $learningPathTreeNode)
    {
        $title = $this->get_application()->getAutomaticNumberingService()
            ->getAutomaticNumberedTitleForLearningPathTreeNode(
                $learningPathTreeNode
            );

        $treeItem = array(
            'key' => $learningPathTreeNode->getId(),
            'title' => $title
        );

        if ($learningPathTreeNode->hasChildNodes())
        {
            $treeItem['folder'] = 'true';
            $treeData = array();

            foreach ($learningPathTreeNode->getChildNodes() as $childLearningPathTreeNode)
            {
                $treeData[] = $this->getTreeNodesAsArray($childLearningPathTreeNode);
            }

            $treeItem['children'] = $treeData;
        }

        return $treeItem;
    }
}
