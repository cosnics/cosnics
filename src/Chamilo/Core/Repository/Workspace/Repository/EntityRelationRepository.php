<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\OperationConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\Workspace\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityRelationRepository
{

    /**
     * @var \Chamilo\Libraries\Storage\Query\Condition\OrCondition[]
     */
    private static array $entitiesConditions = [];

    protected DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function countEntityRelations(?Condition $condition = null): int
    {
        return $this->getDataClassRepository()->count(
            WorkspaceEntityRelation::class, new DataClassCountParameters($condition)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createWorkspaceEntityRelation(WorkspaceEntityRelation $workspaceEntityRelation): bool
    {
        return $this->getDataClassRepository()->create($workspaceEntityRelation);
    }

    public function deleteWorkspaceEntityRelation(WorkspaceEntityRelation $workspaceEntityRelation): bool
    {
        return $this->getDataClassRepository()->delete($workspaceEntityRelation);
    }

    /**
     * @param int[][] $entities
     */
    public function findEntitiesWithRight(array $entities, int $right, Workspace $workspaceImplementation): bool
    {
        $entityRelationConditions = [];

        $entityRelationConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_WORKSPACE_ID
            ), new StaticConditionVariable($workspaceImplementation->getId())
        );

        $entityRelationConditions[] = new EqualityCondition(
            new OperationConditionVariable(
                new PropertyConditionVariable(
                    WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_RIGHTS
                ), OperationConditionVariable::BITWISE_AND, new StaticConditionVariable($right)
            ), new StaticConditionVariable($right)
        );

        $entityRelationConditions[] = $this->getEntitiesCondition($entities);

        $entityRelationCondition = new AndCondition($entityRelationConditions);

        $entityRelationCount = $this->getDataClassRepository()->count(
            WorkspaceEntityRelation::class, new DataClassCountParameters($entityRelationCondition)
        );

        return $entityRelationCount > 0;
    }

    public function findEntityRelationByIdentifier(string $identifier): ?WorkspaceEntityRelation
    {
        return $this->getDataClassRepository()->retrieveById(WorkspaceEntityRelation::class, $identifier);
    }

    public function findEntityRelationForWorkspaceEntityTypeAndIdentifier(
        Workspace $workspace, int $entityType, string $entityIdentifier
    ): ?WorkspaceEntityRelation
    {
        $entityConditions = [];

        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_WORKSPACE_ID
            ), new StaticConditionVariable($workspace->getId())
        );

        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_ENTITY_TYPE
            ), new StaticConditionVariable($entityType)
        );
        $entityConditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_ENTITY_ID
            ), new StaticConditionVariable($entityIdentifier)
        );

        $entityCondition = new AndCondition($entityConditions);

        return $this->getDataClassRepository()->retrieve(
            WorkspaceEntityRelation::class, new DataClassRetrieveParameters($entityCondition)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findEntityRelations(?Condition $condition = null, ?int $limit = null, ?int $offset = null
    ): ArrayCollection
    {
        return $this->getDataClassRepository()->retrieves(
            WorkspaceEntityRelation::class, new DataClassRetrievesParameters($condition, $limit, $offset)
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findEntityRelationsForWorkspace(Workspace $workspace): ArrayCollection
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_WORKSPACE_ID
            ), new StaticConditionVariable($workspace->getId())
        );

        return $this->getDataClassRepository()->retrieves(
            WorkspaceEntityRelation::class, new DataClassRetrievesParameters($condition)
        );
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }

    /**
     * @param int[][] $entities
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\OrCondition
     */
    private function getEntitiesCondition(array $entities): OrCondition
    {
        $entitiesHash = md5(serialize($entities));

        if (!isset(self::$entitiesConditions[$entitiesHash]))
        {
            $entityTypeConditions = [];

            foreach ($entities as $entityType => $entityIdentifiers)
            {
                foreach ($entityIdentifiers as $entityIdentifier)
                {

                    $entityConditions = [];

                    $entityConditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_ENTITY_TYPE
                        ), new StaticConditionVariable($entityType)
                    );
                    $entityConditions[] = new EqualityCondition(
                        new PropertyConditionVariable(
                            WorkspaceEntityRelation::class, WorkspaceEntityRelation::PROPERTY_ENTITY_ID
                        ), new StaticConditionVariable($entityIdentifier)
                    );

                    $entityTypeConditions[] = new AndCondition($entityConditions);
                }
            }

            self::$entitiesConditions[$entitiesHash] = new OrCondition($entityTypeConditions);
        }

        return self::$entitiesConditions[$entitiesHash];
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateWorkspaceEntityRelation(WorkspaceEntityRelation $workspaceEntityRelation): bool
    {
        return $this->getDataClassRepository()->update($workspaceEntityRelation);
    }
}