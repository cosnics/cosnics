<?php

namespace Chamilo\Core\Repository\Workspace\Extension\Office365\Service;

use Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\DataClass\WorkspaceOffice365Reference;
use Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\Repository\WorkspaceOffice365ReferenceRepository;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Workspace\Extension\Office365\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WorkspaceOffice365ReferenceService
{
    /**
     * @var \Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\Repository\WorkspaceOffice365ReferenceRepository
     */
    protected $workspaceOffice365ReferenceRepository;

    /**
     * WorkspaceOffice365Service constructor.
     *
     * @param \Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\Repository\WorkspaceOffice365ReferenceRepository $workspaceOffice365ReferenceRepository
     */
    public function __construct(WorkspaceOffice365ReferenceRepository $workspaceOffice365ReferenceRepository)
    {
        $this->workspaceOffice365ReferenceRepository = $workspaceOffice365ReferenceRepository;
    }

    /**
     * Creates a new reference for a workspace. If the workspace reference is already created the planner reference
     * (if changed) is updated
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param string $office365GroupId
     *
     * @return \Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\DataClass\WorkspaceOffice365Reference
     */
    public function createReferenceForWorkspace(Workspace $workspace, $office365GroupId)
    {
        $workspaceOffice365Reference = new WorkspaceOffice365Reference();
        $workspaceOffice365Reference->setWorkspaceId($workspace->getId());
        $workspaceOffice365Reference->setOffice365GroupId($office365GroupId);
        $workspaceOffice365Reference->setLinked(true);

        if (!$this->workspaceOffice365ReferenceRepository->createReference($workspaceOffice365Reference))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create a new WorkspaceOffice365Reference for workspace %s', $workspace->getId()
                )
            );
        }

        return $workspaceOffice365Reference;
    }

    /**
     * Removes the reference for a workspace
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     */
    public function removeReferenceForWorkspace(Workspace $workspace)
    {
        if (!$this->workspaceOffice365ReferenceRepository->removeReferenceForWorkspace($workspace))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not remove the WorkspaceOffice365Reference for workspace %s', $workspace->getId()
                )
            );
        }
    }

    /**
     * Returns whether or not the workspace is connected to an office365 workspace and linked (active)
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return bool
     */
    public function workspaceHasLinkedReference(Workspace $workspace)
    {
        $workspaceOffice365Reference = $this->getWorkspaceReference($workspace);

        return $workspaceOffice365Reference instanceof WorkspaceOffice365Reference &&
            $workspaceOffice365Reference->isLinked();
    }

    /**
     * Returns whether or not the workspace is connected to an office365 workspace (either linked or unlinked)
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return bool
     */
    public function workspaceHasReference(Workspace $workspace)
    {
        $workspaceOffice365Reference = $this->getWorkspaceReference($workspace);

        return $workspaceOffice365Reference instanceof WorkspaceOffice365Reference;
    }

    /**
     * Returns the Office365 reference object for a workspace
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return \Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\DataClass\WorkspaceOffice365Reference|\Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function getWorkspaceReference(Workspace $workspace)
    {
        return $this->workspaceOffice365ReferenceRepository->findByWorkspace($workspace);
    }

    /**
     * Unlinks the workspace from the office365 workspace. The reference object is never removed but
     * only flagged as unlinked so it can be retrieved in the future to reactivate the connection
     *
     * @param \Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\DataClass\WorkspaceOffice365Reference $workspaceOffice365Reference
     *
     */
    public function unlinkWorkspaceReference(WorkspaceOffice365Reference $workspaceOffice365Reference)
    {
        $workspaceOffice365Reference->setLinked(false);

        if (!$this->workspaceOffice365ReferenceRepository->updateReference($workspaceOffice365Reference))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the WorkspaceOffice365Reference for workspace %s',
                    $workspaceOffice365Reference->getWorkspaceId()
                )
            );
        }
    }

    /**
     * Links the workspace from the office365 workspace. The reference object is never removed but
     * only flagged as unlinked so it can be retrieved in the future to reactivate the connection
     *
     * @param \Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\DataClass\WorkspaceOffice365Reference $workspaceOffice365Reference
     *
     */
    public function linkWorkspaceReference(WorkspaceOffice365Reference $workspaceOffice365Reference)
    {
        $workspaceOffice365Reference->setLinked(true);

        if (!$this->workspaceOffice365ReferenceRepository->updateReference($workspaceOffice365Reference))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the WorkspaceOffice365Reference for workspace %s',
                    $workspaceOffice365Reference->getWorkspaceId()
                )
            );
        }
    }

    /**
     * Stores a planner reference for a workspace
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param string $office365GroupId
     * @param string $office365PlanId
     *
     */
    public function storePlannerReferenceForWorkspace(Workspace $workspace, $office365GroupId, $office365PlanId)
    {
        $workspaceOffice365Reference =
            $this->workspaceOffice365ReferenceRepository->findByWorkspace($workspace);
        if (!$workspaceOffice365Reference)
        {
            $workspaceOffice365Reference = $this->createReferenceForWorkspace($workspace, $office365GroupId);
        }

        if (!empty($office365PlanId) && $office365PlanId != $workspaceOffice365Reference->getOffice365PlanId())
        {
            $workspaceOffice365Reference->setOffice365PlanId($office365PlanId);

            if (!$this->workspaceOffice365ReferenceRepository->updateReference($workspaceOffice365Reference))
            {
                throw new \RuntimeException(
                    sprintf(
                        'Could not update the WorkspaceOffice365Reference for workspace %s', $workspace->getId()
                    )
                );
            }
        }
    }

    /**
     * Removes the planner reference from the workspace reference
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     */
    public function removePlannerFromWorkspace(Workspace $workspace)
    {
        $workspaceOffice365Reference =
            $this->workspaceOffice365ReferenceRepository->findByWorkspace($workspace);

        if (!$workspaceOffice365Reference instanceof WorkspaceOffice365Reference)
        {
            throw new \InvalidArgumentException('The given workspace is not connected to an office365 workspace');
        }

        $workspaceOffice365Reference->setOffice365PlanId(null);

        if (!$this->workspaceOffice365ReferenceRepository->updateReference($workspaceOffice365Reference))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the WorkspaceOffice365Reference for workspace %s', $workspace->getId()
                )
            );
        }

        /*
         * Removes the reference to the workspace because there is no use to store
         * the reference (currently) if planner is removed
         */
        $this->removeReferenceForWorkspace($workspace);
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return bool
     */
    public function workspaceHasPlannerReference(Workspace $workspace)
    {
        $workspaceOffice365Reference =
            $this->workspaceOffice365ReferenceRepository->findByWorkspace($workspace);
        if (!$workspaceOffice365Reference instanceof WorkspaceOffice365Reference ||
            !$workspaceOffice365Reference->isLinked())
        {
            return false;
        }

        return !empty($workspaceOffice365Reference->getOffice365PlanId());
    }
}