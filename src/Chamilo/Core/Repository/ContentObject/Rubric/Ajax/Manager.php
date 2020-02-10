<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax;

use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricService;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\DataClass\Rubric;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const ACTION_ADD_LEVEL = 'AddLevel';
    const ACTION_ADD_TREE_NODE = 'AddTreeNode';
    const ACTION_DELETE_LEVEL = 'DeleteLevel';
    const ACTION_DELETE_TREE_NODE = 'DeleteTreeNode';
    const ACTION_MOVE_LEVEL = 'MoveLevel';
    const ACTION_MOVE_TREE_NODE = 'MoveTreeNode';
    const ACTION_UPDATE_TREE_NODE = 'UpdateTreeNode';

    const PARAM_RUBRIC_DATA_ID = 'RubricId';
    const PARAM_VERSION = 'Version';
    const PARAM_TREE_NODE_DATA = 'TreeNodeData';

    public function getRequiredPostParameters()
    {
        return [self::PARAM_RUBRIC_DATA_ID, self::PARAM_VERSION];
    }

    /**
     * @return RubricService
     */
    protected function getRubricService()
    {
        return $this->getService(RubricService::class);
    }

    /**
     * @param RubricData $rubricData
     *
     * @throws NotAllowedException
     */
    protected function validateRubricDataRights(RubricData $rubricData)
    {
        $rubricId = $rubricData->getContentObjectId();
        $contentObject = $this->getContentObjectRepository()->findById($rubricId);
        if (!$contentObject instanceof Rubric)
        {
            throw new \RuntimeException(
                sprintf('Rubric content object for rubric data %s not found', $rubricData->getId())
            );
        }

        if(!$this->getRightsService()->canEditContentObject($this->getUser(), $contentObject))
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @return ContentObjectRepository
     */
    protected function getContentObjectRepository()
    {
        return $this->getService(ContentObjectRepository::class);
    }

    /**
     * @return RightsService
     */
    protected function getRightsService()
    {
        return $this->getService(RightsService::class);
    }
}
