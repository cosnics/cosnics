<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer\LearningPathTreeJSONMapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\NodeActionGenerator;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns the learning path tree nodes as JSON
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetLearningPathTreeNodesComponent extends Manager
{
    const PARAM_ACTIVE_CHILD_ID = 'active_child_id';
    /**
     * Executes this component and returns its output
     */
    public function run()
    {
        try
        {
            $activeChildId = $this->getRequestedPostDataValue(self::PARAM_ACTIVE_CHILD_ID);

            $learningPathTree = $this->get_application()->getLearningPathTree();
            $activeChildNodeId = $learningPathTree->getLearningPathTreeNodeById((int) $activeChildId);

            $learningPathTreeJSONMapper = new LearningPathTreeJSONMapper(
                $learningPathTree, $this->getUser(),
                $this->get_application()->getLearningPathTrackingService(),
                $this->get_application()->getAutomaticNumberingService(),
                new NodeActionGenerator(Translation::getInstance(), $this->get_application()->get_parameters()),
                $this->get_application()->get_application()->get_learning_path_tree_menu_url(),
                $activeChildNodeId,
                $this->get_application()->get_application()->is_allowed_to_view_content_object(),
                $this->get_application()->canEditLearningPathTreeNode(
                    $this->get_application()->getCurrentLearningPathTreeNode()
                )
            );

            $treeData = $learningPathTreeJSONMapper->getNodes();

            return new JsonResponse($treeData);
        }
        catch (\Exception $ex)
        {
            JsonAjaxResult::general_error();
        }

        return null;
    }

    /**
     * Returns the required post parameters
     *
     * @return string
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_ACTIVE_CHILD_ID);
    }
}
