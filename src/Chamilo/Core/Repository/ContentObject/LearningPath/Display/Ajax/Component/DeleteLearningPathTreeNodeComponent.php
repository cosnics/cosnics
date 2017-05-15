<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Renderer\LearningPathTreeJSONMapper;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeBaseActionGenerator;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Translation;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DeleteLearningPathTreeNodeComponent
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 */
class DeleteLearningPathTreeNodeComponent extends Manager
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

            $tree = $this->get_application()->getLearningPathTree();
            $node = $tree->getLearningPathTreeNodeById((int) $nodeId);

            $learningPathChildService = $this->get_application()->getLearningPathChildService();
            $learningPathChildService->deleteContentObjectFromLearningPath($node);

            return new JsonResponse();
        }
        catch (\Exception $ex)
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
        return array(self::PARAM_NODE_ID);
    }
}