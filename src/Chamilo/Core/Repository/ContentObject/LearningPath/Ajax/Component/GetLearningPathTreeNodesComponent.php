<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTreeBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Translation;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns the learning path tree nodes as
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetLearningPathTreeNodesComponent extends Manager
{
    const PARAM_LEARNING_PATH_ID = 'LearningPathId';

    /**
     * Executes this component and returns its output
     */
    public function run()
    {
        $learningPathId = $this->getRequestedPostDataValue(self::PARAM_LEARNING_PATH_ID);
        $learningPath =
            \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(LearningPath::class_name(), $learningPathId);

        if (!$learningPath instanceof LearningPath)
        {
            throw new ObjectNotExistException(
                Translation::getInstance()->getTranslation('LearningPath'), $learningPathId
            );
        }

        $learningPathTree = $this->getLearningPathTreeBuilder()->buildLearningPathTree($learningPath);
        $treeData = $this->getTreeDataForNode($learningPathTree->getRoot());

        return new JsonResponse($treeData);
    }

    /**
     * Builds the data from a LearningPathTreeNode as an array
     *
     * @param LearningPathTreeNode $learningPathTreeNode
     *
     * @return array
     */
    protected function getTreeDataForNode(LearningPathTreeNode $learningPathTreeNode)
    {
        $title = $this->getAutomaticNumberingService()->getAutomaticNumberedTitleForLearningPathTreeNode(
            $learningPathTreeNode
        );

        $treeItem = array(
            'key' => $learningPathTreeNode->getId(),
            'title' => $title
        );

        if($learningPathTreeNode->hasChildNodes())
        {
            $treeItem['folder'] = 'true';
            $treeData = array();

            foreach($learningPathTreeNode->getChildNodes() as $childLearningPathTreeNode)
            {
                $treeData[] = $this->getTreeDataForNode($childLearningPathTreeNode);
            }

            $treeItem['children'] = $treeData;
        }

        return $treeItem;
    }

    /**
     * Returns the required post parameters
     *
     * @return array
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_LEARNING_PATH_ID);
    }

    /**
     * Returns the LearningPathTreeBuilder service
     *
     * @return LearningPathTreeBuilder | object
     */
    protected function getLearningPathTreeBuilder()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.learning_path_tree_builder'
        );
    }

    /**
     * @return AutomaticNumberingService | object
     */
    public function getAutomaticNumberingService()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.automatic_numbering_service'
        );
    }
}
