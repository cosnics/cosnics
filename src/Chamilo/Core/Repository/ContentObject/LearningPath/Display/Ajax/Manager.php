<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Ajax;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component\AjaxComponent;
use Chamilo\Libraries\Architecture\AjaxManager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use RuntimeException;

/**
 * Class Manager
 *
 * @author pjbro <pjbro@users.noreply.github.com>
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const PARAM_ACTION = 'LearningPathAjaxAction';

    const ACTION_GET_LEARNING_PATH_TREE_NODES = 'GetTreeNodes';
    const ACTION_MOVE_LEARNING_PATH_TREE_NODE = 'MoveTreeNode';
    const ACTION_ADD_LEARNING_PATH_TREE_NODE = 'AddTreeNode';
    const ACTION_UPDATE_LEARNING_PATH_TREE_NODE_TITLE = 'UpdateTreeNodeTitle';
    const ACTION_DELETE_LEARNING_PATH_TREE_NODE = 'DeleteTreeNode';

    const DEFAULT_ACTION = self::ACTION_GET_LEARNING_PATH_TREE_NODES;

    /**
     * @param \Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        if(!$this->get_application() instanceof AjaxComponent)
        {
            throw new RuntimeException(
                'The LearningPath display ajax application should only be run from ' .
                'the LearningPath display AjaxComponent'
            );
        }
    }

    /**
     * @return AjaxComponent
     */
    public function get_application()
    {
        return parent::get_application();
    }
}
