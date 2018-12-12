<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ApplicationFactory extends \Chamilo\Libraries\Architecture\Factory\ApplicationFactory
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface
     */
    protected $assignmentServiceBridge;

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface $assignmentServiceBridge
     */
    public function setAssignmentServiceBridge(AssignmentServiceBridgeInterface $assignmentServiceBridge)
    {
        $this->assignmentServiceBridge = $assignmentServiceBridge;
    }

    public function getDefaultAction($context)
    {
        if($this->assignmentServiceBridge->canEditAssignment())
        {
            return Manager::ACTION_VIEW;
        }

        return Manager::ACTION_ENTRY;
    }

}