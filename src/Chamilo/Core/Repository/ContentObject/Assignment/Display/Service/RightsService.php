<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RightsService
{
    /**
     * @var AssignmentServiceBridgeInterface
     */
    protected $assignmentServiceBridge;

    /**
     * @param AssignmentServiceBridgeInterface $assignmentServiceBridge
     */
    public function setAssignmentServiceBridge(AssignmentServiceBridgeInterface $assignmentServiceBridge)
    {
        $this->assignmentServiceBridge = $assignmentServiceBridge;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     *
     * @return bool
     */
    public function canUserViewEntry(User $user, Assignment $assignment, Entry $entry)
    {
        return $this->canUserViewEntity($user, $assignment, $entry->getEntityType(), $entry->getEntityId());
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function canUserViewEntity(User $user, Assignment $assignment, $entityType, $entityId)
    {
        if($this->assignmentServiceBridge->canEditAssignment())
        {
            return true;
        }

        if($assignment->get_visibility_submissions())
        {
            return true;
        }

        return $this->assignmentServiceBridge->isUserPartOfEntity($user, $entityType, $entityId);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function canUserCreateEntry(User $user, Assignment $assignment, $entityType, $entityId)
    {
        return $this->assignmentServiceBridge->isUserPartOfEntity($user, $entityType, $entityId);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     *
     * @return bool
     */
    public function canUserViewEntityBrowser(User $user, Assignment $assignment)
    {
        if($assignment->get_visibility_submissions())
        {
            return true;
        }

        return $this->assignmentServiceBridge->canEditAssignment();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param int $entityType
     * @param int $entityId
     *
     * @return bool
     */
    public function canUserDownloadEntriesFromEntity(User $user, Assignment $assignment, $entityType, $entityId)
    {
        if($this->assignmentServiceBridge->canEditAssignment())
        {
            return true;
        }

        return $this->assignmentServiceBridge->isUserPartOfEntity($user, $entityType, $entityId);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     *
     * @return bool
     */
    public function canUserDownloadAllEntries(User $user, Assignment $assignment)
    {
        return $this->assignmentServiceBridge->canEditAssignment();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     *
     * @return bool
     */
    public function canUserDeleteEntries(User $user, Assignment $assignment)
    {
        return $this->assignmentServiceBridge->canEditAssignment();
    }

}