<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DeleteTreeNodeComponent
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 */
class DeleteTreeNodeComponent extends Manager
{
    const PARAM_NODE_ID = 'node_id';

    /**
     * Runs this component and returns it's response
     */
    function run()
    {
        try
        {
            $nodeId = $this->getRequestedPostDataValue(self::PARAM_NODE_ID);

            $tree = $this->get_application()->getTree();
            $node = $tree->getTreeNodeById((int) $nodeId);

            $learningPathService = $this->get_application()->getLearningPathService();
            $learningPathService->deleteContentObjectFromLearningPath($node);

            return new JsonResponse();
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
        return array(self::PARAM_NODE_ID);
    }
}