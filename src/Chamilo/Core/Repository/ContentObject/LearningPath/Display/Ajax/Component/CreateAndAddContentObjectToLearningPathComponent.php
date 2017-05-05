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
class CreateAndAddContentObjectToLearningPathComponent extends Manager
{
    /**
     * Runs this component and returns it's response
     */
    function run()
    {
        try
        {
            $learningPathChildService = $this->get_application()->getLearningPathChildService();
            $learningPathChild = $learningPathChildService->createAndAddContentObjectToLearningPath(
                Section::class, $this->get_application()->get_root_content_object(),
                $this->get_application()->getCurrentLearningPathTreeNode(), $this->getUser()
            );

            $learningPathTreeBuilder = $this->get_application()->getLearningPathTreeBuilder();
            $learningPathTree =
                $learningPathTreeBuilder->buildLearningPathTree($this->get_application()->get_root_content_object());

            $learningPathTreeJSONMapper = new LearningPathTreeJSONMapper(
                $learningPathTree, $this->getUser(),
                $this->get_application()->getLearningPathTrackingService(),
                $this->get_application()->getAutomaticNumberingService(),
                new NodeActionGenerator(Translation::getInstance(), $this->get_application()->get_parameters()),
                $this->get_application()->get_application()->get_learning_path_tree_menu_url(),
                $this->get_application()->getCurrentLearningPathTreeNode(),
                $this->get_application()->get_application()->is_allowed_to_view_content_object(),
                $this->get_application()->canEditLearningPathTreeNode(
                    $this->get_application()->getCurrentLearningPathTreeNode()
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
}