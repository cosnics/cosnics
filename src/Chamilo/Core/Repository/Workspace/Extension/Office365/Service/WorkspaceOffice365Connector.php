<?php

namespace Chamilo\Core\Repository\Workspace\Extension\Office365\Service;

use Chamilo\Core\Repository\Workspace\Extension\Office365\Service\WorkspaceOffice365ReferenceService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService;
use Chamilo\Libraries\Protocol\Microsoft\Graph\Service\UserService;

/**
 * @package Chamilo\Core\Repository\Workspace\Extension\Office365\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WorkspaceOffice365Connector
{
    /**
     * @var \Chamilo\Core\Repository\Workspace\Extension\Office365\Service\WorkspaceOffice365ReferenceService
     */
    protected $workspaceOffice365ReferenceService;

    /**
     * @var \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService
     */
    protected $graphGroupService;

    /**
     * WorkspaceOffice365Connector constructor.
     *
     * @param \Chamilo\Core\Repository\Workspace\Extension\Office365\Service\WorkspaceOffice365ReferenceService $workspaceOffice365ReferenceService
     * @param \Chamilo\Libraries\Protocol\Microsoft\Graph\Service\GroupService $graphGroupService
     */
    public function __construct(
        WorkspaceOffice365ReferenceService $workspaceOffice365ReferenceService,
        GroupService $graphGroupService
    )
    {
        $this->workspaceOffice365ReferenceService = $workspaceOffice365ReferenceService;
        $this->graphGroupService = $graphGroupService;
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return bool
     */
    public function isOffice365GroupActiveForWorkspace(Workspace $workspace)
    {
        return $this->workspaceOffice365ReferenceService->workspaceHasLinkedReference($workspace);
    }

    /**
     * Creates an office365 group for a given workspace
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function createGroupForWorkspace(Workspace $workspace, User $user)
    {
        if ($this->workspaceOffice365ReferenceService->workspaceHasReference($workspace))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create a new office365 group for the given workspace %s' .
                    'since there is a group already available'
                ), $workspace->getId()
            );
        }

        $groupId = $this->graphGroupService->createGroupByName($user, $workspace->getName());

        $this->workspaceOffice365ReferenceService->createReferenceForWorkspace($workspace, $groupId);
        $this->graphGroupService->addMemberToGroup($groupId, $user);
    }

    /**
     * Returns the link for the group space and makes sure that the user has access to it
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     *
     * @throws \Chamilo\Libraries\Protocol\Microsoft\Graph\Exception\AzureUserNotExistsException
     */
    public function getGroupUrlForVisit(Workspace $workspace, User $user)
    {
        if (!$this->isOffice365GroupActiveForWorkspace($workspace))
        {
            throw new \RuntimeException();
        }

        $reference = $this->workspaceOffice365ReferenceService->getWorkspaceReference($workspace);

        $this->graphGroupService->addMemberToGroup($reference->getOffice365GroupId(), $user);
        return $this->graphGroupService->getGroupUrl($reference->getOffice365GroupId());
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     */
    public function updateGroupNameForWorkspace(Workspace $workspace)
    {
        if (!$this->isOffice365GroupActiveForWorkspace($workspace))
        {
            return;
        }

        $reference = $this->workspaceOffice365ReferenceService->getWorkspaceReference($workspace);

        $this->graphGroupService->updateGroupName($reference->getOffice365GroupId(), $workspace->getName());
    }

    /**
     * Unlinks the given course group with the referenced office365 group
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function unlinkOffice365GroupFromWorkspace(Workspace $workspace, User $user)
    {
        if (!$this->isOffice365GroupActiveForWorkspace($workspace))
        {
            return;
        }

        $reference = $this->workspaceOffice365ReferenceService->getWorkspaceReference($workspace);
        $this->graphGroupService->removeAllMembersFromGroup($reference->getOffice365GroupId());

        try
        {
            $this->graphGroupService->addMemberToGroup($reference->getOffice365GroupId(), $user);
        }
        catch (AzureUserNotExistsException $ex)
        {

        }

        $this->workspaceOffice365ReferenceService->unlinkWorkspaceReference($reference);
    }
}