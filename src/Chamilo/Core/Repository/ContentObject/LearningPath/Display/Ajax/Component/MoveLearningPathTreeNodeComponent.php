<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Moves the LearningPathTreeNode to a new parent and with a new display order
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MoveLearningPathTreeNodeComponent extends Manager
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

            $learningPathTree = $this->get_application()->getLearningPathTree();
            $learningPathTreeNode = $learningPathTree->getLearningPathTreeNodeById((int) $childId);

            $learningPathChildService = $this->get_application()->getLearningPathChildService();

            if (!$this->get_application()->canEditLearningPathTreeNode($learningPathTreeNode))
            {
                throw new NotAllowedException();
            }

            $parentId = $this->getRequestedPostDataValue(self::PARAM_PARENT_ID);
            $displayOrder = $this->getRequestedPostDataValue(self::PARAM_DISPLAY_ORDER);

            if (!isset($parentId) || !isset($displayOrder))
            {
                throw new \RuntimeException(
                    'For the direct mover to work you need to specify a parent and a display order'
                );
            }

            $path = $this->get_application()->getLearningPathTree();
            $parentNode = $path->getLearningPathTreeNodeById((int) $parentId);

            $learningPathChildService->moveContentObjectToOtherLearningPath(
                $learningPathTreeNode, $parentNode, $displayOrder
            );

            return new JsonResponse(null, 200);
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
        return array(self::PARAM_PARENT_ID, self::PARAM_DISPLAY_ORDER, self::PARAM_CHILD_ID);
    }
}