<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer\LearningPathTreeJSONMapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\NodeActionGenerator;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Ajax call to create a new content object of a given type with placeholder data for the given user and add them
 * to the current learning path and selected learning path node
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AddLearningPathTreeNodeComponent extends Manager
{
    const PARAM_PARENT_NODE_ID = 'parent_node_id';
    const PARAM_NODE_TYPE = 'node_type'; //content object type.
    const PARAM_DISPLAY_ORDER = 'display_order'; //optional

    /**
     * Runs this component and returns it's response
     */
    function run()
    {
        try
        {
            $parentNodeId = $this->getRequestedPostDataValue(self::PARAM_PARENT_NODE_ID);
            $nodeType = $this->getRequestedPostDataValue(self::PARAM_NODE_TYPE);

            $tree = $this->get_application()->getLearningPathTree();
            $parentNode = $tree->getLearningPathTreeNodeById((int) $parentNodeId);

            $learningPathChildService = $this->get_application()->getLearningPathChildService();
            $learningPathChild = $learningPathChildService->createAndAddContentObjectToLearningPath(
                $nodeType, $this->get_application()->get_root_content_object(),
                $parentNode, $this->getUser()
            );

            $displayOrder = $this->getRequest()->request->get(self::PARAM_DISPLAY_ORDER);
            if(!empty($displayOrder)) {
                $learningPathChild->setDisplayOrder((int) $displayOrder);
                $learningPathChild->update();
            }

            $learningPathTreeBuilder = $this->get_application()->getLearningPathTreeBuilder();
            $tree =
                $learningPathTreeBuilder->buildLearningPathTree($this->get_application()->get_root_content_object());

            $learningPathTreeJSONMapper = new LearningPathTreeJSONMapper(
                $tree, $this->getUser(),
                $this->get_application()->getLearningPathTrackingService(),
                $this->get_application()->getAutomaticNumberingService(),
                new NodeActionGenerator(Translation::getInstance(), $this->get_application()->get_parameters()),
                $this->get_application()->get_application()->get_learning_path_tree_menu_url(),
                $tree->getLearningPathTreeNodeById((int) $learningPathChild->getId()),
                $this->get_application()->get_application()->is_allowed_to_view_content_object(),
                $this->get_application()->canEditLearningPathTreeNode(
                    $tree->getLearningPathTreeNodeById((int) $learningPathChild->getId())
                )
            );

            $treeData = $learningPathTreeJSONMapper->getNodes();

            return new JsonResponse(array('treeData' => $treeData, 'nodeId' => $learningPathChild->getId()));
        }
        catch (\Exception $ex)
        {
            return new JsonResponse(null, 500);
        }
    }

    /**
     * Returns the required post parameters
     *
     * @return string
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_PARENT_NODE_ID, self::PARAM_NODE_TYPE);
    }
}