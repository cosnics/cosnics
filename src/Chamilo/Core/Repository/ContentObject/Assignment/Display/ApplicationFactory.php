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

    protected int $viewAssignmentEntityType;
    protected int $viewAssignmentEntityId;

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface $assignmentServiceBridge
     */
    public function setAssignmentServiceBridge(AssignmentServiceBridgeInterface $assignmentServiceBridge)
    {
        $this->assignmentServiceBridge = $assignmentServiceBridge;
    }

    public function setViewAssignmentEntity(int $entityType, int $entityId)
    {
        $this->viewAssignmentEntityType = $entityType;
        $this->viewAssignmentEntityId = $entityId;
    }

    public function getDefaultAction($context)
    {
        if(!empty($this->viewAssignmentEntityId))
        {
            $this->getRequest()->query->set(Manager::PARAM_ENTITY_TYPE, $this->viewAssignmentEntityType);
            $this->getRequest()->query->set(Manager::PARAM_ENTITY_ID, $this->viewAssignmentEntityId);

            return Manager::ACTION_ENTRY;
        }

        if($this->assignmentServiceBridge->canEditAssignment())
        {
            return Manager::ACTION_VIEW;
        }

        return Manager::ACTION_ENTRY;
    }

}
