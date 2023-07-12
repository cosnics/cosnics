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
 * Returns the learning path tree nodes as JSON
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class GetTreeNodesComponent extends Manager
{
    public const PARAM_ACTIVE_CHILD_ID = 'active_child_id';

    /**
     * Executes this component and returns its output
     */
    public function run()
    {
        try
        {
            $activeChildId = $this->getRequestedPostDataValue(self::PARAM_ACTIVE_CHILD_ID);

            $tree = $this->get_application()->getTree();
            $activeChildNode = $tree->getTreeNodeById((int) $activeChildId);

            $nodeActionGeneratorFactory = new NodeActionGeneratorFactory(
                Translation::getInstance(), $this->getRegistrationConsulter(), ClassnameUtilities::getInstance(),
                $this->get_application()->get_parameters()
            );

            $treeJSONMapper = new TreeJSONMapper(
                $tree, $this->getUser(), $this->get_application()->getTrackingService(),
                $this->get_application()->getAutomaticNumberingService(),
                $nodeActionGeneratorFactory->createNodeActionGenerator(),
                $this->get_application()->get_application()->get_tree_menu_url(), $activeChildNode,
                $this->get_application()->get_application()->is_allowed_to_view_content_object(),
                $this->get_application()->canEditTreeNode(
                    $this->get_application()->getCurrentTreeNode()
                )
            );

            $treeData = $treeJSONMapper->getNodes();

            return new JsonResponse($treeData);
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
        return [self::PARAM_ACTIVE_CHILD_ID];
    }
}
