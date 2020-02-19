<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax;

use Chamilo\Core\Repository\ContentObject\Rubric\Display\Component\AjaxComponent;
use Chamilo\Core\Repository\ContentObject\Rubric\Service\RubricAjaxService;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Response\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

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
    const PARAM_NEW_PARENT_ID = 'NewParentId';
    const PARAM_NEW_SORT = 'NewSort';
    const PARAM_LEVEL_DATA = 'LevelData';
    const PARAM_CHOICE_DATA = 'ChoiceData';

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if ($applicationConfiguration->getApplication() instanceof AjaxComponent)
        {
            throw new \RuntimeException(
                'This component can only be run through the ajax component from the rubric complex display'
            );
        }
        parent::__construct($applicationConfiguration);
    }

    /**
     * @return string|Response
     */
    function run()
    {
        try
        {
            $result = $this->runAjaxComponent();

            return new JsonResponse($this->getSerializer()->serialize($result, 'json'), 200, [], true);
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);

            return new AjaxExceptionResponse($ex);
        }
    }

    /**
     * @return array
     */
    abstract function runAjaxComponent();

    public function getRequiredPostParameters()
    {
        return [self::PARAM_RUBRIC_DATA_ID, self::PARAM_VERSION];
    }

    /**
     * @return RubricAjaxService
     */
    protected function getRubricAjaxService()
    {
        return $this->getService(RubricAjaxService::class);
    }

    /**
     * @return int
     */
    protected function getRubricDataId()
    {
        return $this->getRequest()->getFromPost(self::PARAM_RUBRIC_DATA_ID);
    }

    /**
     * @return int
     */
    protected function getVersion()
    {
        return $this->getRequest()->getFromPost(self::PARAM_VERSION);
    }

    /**
     * @return string
     */
    protected function getTreeNodeData()
    {
        return $this->getRequest()->getFromPost(self::PARAM_TREE_NODE_DATA);
    }

    /**
     * @return string
     */
    protected function getLevelData()
    {
        return $this->getRequest()->getFromPost(self::PARAM_LEVEL_DATA);
    }

    /**
     * @return string
     */
    protected function getChoiceData()
    {
        return $this->getRequest()->getFromPost(self::PARAM_CHOICE_DATA);
    }
}
