<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeActionGeneratorFactory;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeJSONMapper;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Ajax call to create a new content object of a given type with placeholder data for the given user and add them
 * to the current learning path and selected learning path node
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AddTreeNodeComponent extends Manager
{
    public const PARAM_DISPLAY_ORDER = 'display_order';

    public const PARAM_NODE_TYPE = 'node_type'; //content object type.

    public const PARAM_PARENT_NODE_ID = 'parent_node_id'; //optional

    /**
     * Runs this component and returns it's response
     */
    public function run()
    {
        try
        {
            $parentNodeId = $this->getRequestedPostDataValue(self::PARAM_PARENT_NODE_ID);
            $nodeType = $this->getRequestedPostDataValue(self::PARAM_NODE_TYPE);

            $tree = $this->get_application()->getTree();
            $parentNode = $tree->getTreeNodeById((int) $parentNodeId);

            $learningPathService = $this->get_application()->getLearningPathService();
            $treeNodeData = $learningPathService->createAndAddContentObjectToLearningPath(
                $nodeType, $this->get_application()->get_root_content_object(), $parentNode, $this->getUser()
            );

            $displayOrder = $this->getRequest()->request->get(self::PARAM_DISPLAY_ORDER);
            if (!empty($displayOrder))
            {
                $treeNodeData->setDisplayOrder((int) $displayOrder);
                $treeNodeData->update();
            }

            $learningPathService = $this->get_application()->getLearningPathService();
            $tree = $learningPathService->buildTree($this->get_application()->get_root_content_object());

            $nodeActionGeneratorFactory = new NodeActionGeneratorFactory(
                Translation::getInstance(), $this->getRegistrationConsulter(), ClassnameUtilities::getInstance(),
                $this->get_application()->get_parameters()
            );

            $treeJSONMapper = new TreeJSONMapper(
                $tree, $this->getUser(), $this->get_application()->getTrackingService(),
                $this->get_application()->getAutomaticNumberingService(),
                $nodeActionGeneratorFactory->createNodeActionGenerator(),
                $this->get_application()->get_application()->get_tree_menu_url(),
                $tree->getTreeNodeById((int) $treeNodeData->getId()),
                $this->get_application()->get_application()->is_allowed_to_view_content_object(),
                $this->get_application()->canEditTreeNode(
                    $tree->getTreeNodeById((int) $treeNodeData->getId())
                )
            );

            $treeData = $treeJSONMapper->getNodes();

            return new JsonResponse(['treeData' => $treeData, 'nodeId' => $treeNodeData->getId()]);
        }
        catch (Exception $ex)
        {
            return $this->handleException($ex);
        }
    }

    /**
     * Returns the required post parameters
     *
     * @return string
     */
    public function getRequiredPostParameters(array $postParameters = []): array
    {
        return [self::PARAM_PARENT_NODE_ID, self::PARAM_NODE_TYPE];
    }
}