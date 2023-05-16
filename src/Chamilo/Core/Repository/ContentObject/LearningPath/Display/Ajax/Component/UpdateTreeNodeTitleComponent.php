<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Changes the title of a given TreeNode
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UpdateTreeNodeTitleComponent extends Manager
{
    const PARAM_NEW_TITLE = 'new_title';
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

            $learningPathService->updateContentObjectTitle(
                $treeNode, $this->getRequestedPostDataValue(self::PARAM_NEW_TITLE)
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
    public function getRequiredPostParameters(): array
    {
        return array(self::PARAM_NEW_TITLE, self::PARAM_CHILD_ID);
    }
}