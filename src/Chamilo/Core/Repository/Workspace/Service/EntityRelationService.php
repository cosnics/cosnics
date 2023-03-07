<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityRelationService
{

    private EntityRelationRepository $entityRelationRepository;

    public function __construct(EntityRelationRepository $entityRelationRepository)
    {
        $this->entityRelationRepository = $entityRelationRepository;
    }

    public function countEntityRelations(?Condition $condition = null): int
    {
        return $this->getEntityRelationRepository()->countEntityRelations($condition);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function createEntityRelation(string $workspaceId, int $entityType, string $entityIdentifier, int $right
    ): bool
    {
        $workspaceEntityRelation = new WorkspaceEntityRelation();
        $this->setEntityRelationProperties(
            $workspaceEntityRelation, $workspaceId, $entityType, $entityIdentifier, $right
        );

        return $this->getEntityRelationRepository()->createWorkspaceEntityRelation($workspaceEntityRelation);
    }

    public function deleteEntityRelation(WorkspaceEntityRelation $entityRelation): bool
    {
        return $this->getEntityRelationRepository()->deleteWorkspaceEntityRelation($entityRelation);
    }

    public function getEntityRelationByIdentifier(string $identifier): ?WorkspaceEntityRelation
    {
        return $this->getEntityRelationRepository()->findEntityRelationByIdentifier($identifier);
    }

    public function getEntityRelationForWorkspaceEntityTypeAndIdentifier(
        Workspace $workspace, int $entityType, string $entityIdentifier
    ): ?WorkspaceEntityRelation
    {
        return $this->getEntityRelationRepository()->findEntityRelationForWorkspaceEntityTypeAndIdentifier(
            $workspace, $entityType, $entityIdentifier
        );
    }

    public function getEntityRelationRepository(): EntityRelationRepository
    {
        return $this->entityRelationRepository;
    }

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $limit
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getEntityRelations(
        ?Condition $condition = null, ?int $limit = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->getEntityRelationRepository()->findEntityRelations($condition, $limit, $offset, $orderBy);
    }

    /**
     * @param int[][] $entities
     */
    public function hasRight(array $entities, int $right, WorkspaceInterface $workspaceImplementation): bool
    {
        return $this->getEntityRelationRepository()->findEntitiesWithRight($entities, $right, $workspaceImplementation);
    }

    private function setEntityRelationProperties(
        WorkspaceEntityRelation $workspaceEntityRelation, string $workspaceId, int $entityType,
        string $entityIdentifier, int $right
    )
    {
        $workspaceEntityRelation->set_workspace_id($workspaceId);
        $workspaceEntityRelation->set_entity_type($entityType);
        $workspaceEntityRelation->set_entity_id($entityIdentifier);
        $workspaceEntityRelation->set_rights($right);
    }

    /**
     * @param string[][] $selectedEntityTypeIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function setEntityRelations(Workspace $workspace, array $selectedEntityTypeIdentifiers, int $right): bool
    {
        foreach ($selectedEntityTypeIdentifiers as $entityType => $entityIdentifiers)
        {
            foreach ($entityIdentifiers as $entityIdentifier)
            {
                $entityRelation = $this->getEntityRelationForWorkspaceEntityTypeAndIdentifier(
                    $workspace, $entityType, $entityIdentifier
                );

                if ($entityRelation instanceof WorkspaceEntityRelation)
                {
                    $success = $this->updateEntityRelation(
                        $entityRelation, $workspace->getId(), $entityType, $entityIdentifier, $right
                    );
                }
                else
                {
                    $success = $this->createEntityRelation($workspace->getId(), $entityType, $entityIdentifier, $right);
                }

                if (!$success)
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function updateEntityRelation(
        WorkspaceEntityRelation $workspaceEntityRelation, string $workspaceId, int $entityType,
        string $entityIdentifier, int $right
    ): bool
    {
        $this->setEntityRelationProperties(
            $workspaceEntityRelation, $workspaceId, $entityType, $entityIdentifier, $right
        );

        return $this->getEntityRelationRepository()->updateWorkspaceEntityRelation($workspaceEntityRelation);
    }
}