<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider;
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
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider
     */
    protected $assignmentDataProvider;

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Interfaces\AssignmentDataProvider $assignmentDataProvider
     */
    public function setAssignmentDataProvider(AssignmentDataProvider $assignmentDataProvider)
    {
        $this->assignmentDataProvider = $assignmentDataProvider;
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
        if($this->assignmentDataProvider->canEditAssignment())
        {
            return true;
        }

        if($assignment->get_visibility_submissions())
        {
            return true;
        }

        return $this->assignmentDataProvider->isUserPartOfEntity($user, $entityType, $entityId);
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
        return $this->assignmentDataProvider->isUserPartOfEntity($user, $entityType, $entityId);
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

        return $this->assignmentDataProvider->canEditAssignment();
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
        if($this->assignmentDataProvider->canEditAssignment())
        {
            return true;
        }

        return $this->assignmentDataProvider->isUserPartOfEntity($user, $entityType, $entityId);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     *
     * @return bool
     */
    public function canUserDownloadAllEntries(User $user, Assignment $assignment)
    {
        return $this->assignmentDataProvider->canEditAssignment();
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     *
     * @return bool
     */
    public function canUserDeleteEntries(User $user, Assignment $assignment)
    {
        return $this->assignmentDataProvider->canEditAssignment();
    }

}