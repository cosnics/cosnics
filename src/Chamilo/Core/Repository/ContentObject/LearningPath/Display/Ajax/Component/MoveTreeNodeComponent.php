<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Moves the TreeNode to a new parent and with a new display order
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MoveTreeNodeComponent extends Manager
{
    const PARAM_PARENT_ID = 'parent_id';
    const PARAM_DISPLAY_ORDER = 'display_order';
    const PARAM_CHILD_ID = 'child_id';

    /**
     * Executes this component and returns its output
     */
    public function run()
    {
        try
        {
            $childId = $this->getRequestedPostDataValue(self::PARAM_CHILD_ID);

            $tree = $this->get_application()->getTree();
            $treeNode = $tree->getTreeNodeById((int) $childId);

            $learningPathService = $this->get_application()->getLearningPathService();

            if (!$this->get_application()->canEditTreeNode($treeNode))
            {
                throw new NotAllowedException();
            }

            $parentId = $this->getRequestedPostDataValue(self::PARAM_PARENT_ID);
            $displayOrder = $this->getRequestedPostDataValue(self::PARAM_DISPLAY_ORDER);

            if (!isset($parentId) || !isset($displayOrder))
            {
                throw new RuntimeException(
                    'For the direct mover to work you need to specify a parent and a display order'
                );
            }

            $path = $this->get_application()->getTree();
            $parentNode = $path->getTreeNodeById((int) $parentId);

            $learningPathService->moveContentObjectToNewParent(
                $treeNode, $parentNode, $displayOrder
            );

            return new JsonResponse(null, 200);
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
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_PARENT_ID, self::PARAM_DISPLAY_ORDER, self::PARAM_CHILD_ID);
    }
}