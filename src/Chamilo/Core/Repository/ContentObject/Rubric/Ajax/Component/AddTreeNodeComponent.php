<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AddTreeNodeComponent extends Manager
{
    /**
     * @return string
     */
    function run()
    {
        try
        {
            $rubricDataId = $this->getRequest()->getFromPost(self::PARAM_RUBRIC_DATA_ID);
            $versionId = $this->getRequest()->getFromPost(self::PARAM_VERSION);

            $treeNodeData = $this->getSerializer()->deserialize(
                $this->getRequest()->getFromPost(self::PARAM_TREE_NODE_DATA), TreeNodeJSONModel::class, 'json'
            );

            if (!$treeNodeData instanceof TreeNodeJSONModel)
            {
                throw new RuntimeException('Could not parse the tree node JSON model');
            }

            $rubricData = $this->getRubricService()->getRubric($rubricDataId, $versionId);

            $parentTreeNode = $rubricData->getParentNodeById($treeNodeData->getParentId());
            $treeNode = $treeNodeData->toTreeNode($rubricData);
            $parentTreeNode->addChild($treeNode);

            $this->getRubricService()->saveRubric($rubricData);

            return new JsonResponse(
                [
                    'rubric' => ['id' => $rubricData->getId(), 'version' => $rubricData->getVersion()],
                    'tree_node' => $treeNode->toJSONModel()->toJSON($this->getSerializer())
                ]
            );
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);

            return new AjaxExceptionResponse($ex);
        }
    }

    /**
     * @return array|string[]
     */
    public function getRequiredPostParameters()
    {
        $parameters = parent::getRequiredPostParameters();
        $parameters[] = self::PARAM_TREE_NODE_DATA;

        return $parameters;
    }

}
