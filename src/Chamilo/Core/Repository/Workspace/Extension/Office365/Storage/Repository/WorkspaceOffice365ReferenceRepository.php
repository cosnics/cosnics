<?php
namespace Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\DataClass\WorkspaceOffice365Reference;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\StorageParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Repository to manage WorkspaceOffice365Reference objects
 *
 * @package Chamilo\Core\Repository\Workspace\Extension\Office365\Storage\Repository
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WorkspaceOffice365ReferenceRepository extends CommonDataClassRepository
{
    /**
     * Creates a new reference object
     *
     * @param WorkspaceOffice365Reference $workspaceOffice365Reference
     *
     * @return bool
     */
    public function createReference(WorkspaceOffice365Reference $workspaceOffice365Reference)
    {
        $this->dataClassRepository->getDataClassRepositoryCache()->truncateClass(WorkspaceOffice365Reference::class);

        return $this->dataClassRepository->create($workspaceOffice365Reference);
    }

    /**
     * Finds a reference by a given course group
     *
     * @param Workspace $workspace
     *
     * @return WorkspaceOffice365Reference | \Chamilo\Libraries\Storage\DataClass\DataClass
     */
    public function findByWorkspace(Workspace $workspace)
    {
        $condition = $this->getConditionByWorkspace($workspace);

        return $this->dataClassRepository->retrieve(
            WorkspaceOffice365Reference::class, new StorageParameters(condition: $condition)
        );
    }

    /**
     * Builds the condition for a reference by a given course group
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    protected function getConditionByWorkspace(Workspace $workspace)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceOffice365Reference::class, WorkspaceOffice365Reference::PROPERTY_WORKSPACE_ID
            ), new StaticConditionVariable($workspace->getId())
        );
    }

    /**
     * Removes a reference by a given course group
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return bool
     */
    public function removeReferenceForWorkspace(Workspace $workspace)
    {
        $condition = $this->getConditionByWorkspace($workspace);

        return $this->dataClassRepository->deletes(WorkspaceOffice365Reference::class, $condition);
    }

    /**
     * Updates an existing reference object
     *
     * @param WorkspaceOffice365Reference $workspaceOffice365Reference
     *
     * @return bool
     */
    public function updateReference(WorkspaceOffice365Reference $workspaceOffice365Reference)
    {
        return $this->dataClassRepository->update($workspaceOffice365Reference);
    }
}