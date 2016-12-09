<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityRelationService
{

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository
     */
    private $entityRelationRepository;

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository $entityRelationRepository
     */
    public function __construct(EntityRelationRepository $entityRelationRepository)
    {
        $this->entityRelationRepository = $entityRelationRepository;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository
     */
    public function getEntityRelationRepository()
    {
        return $this->entityRelationRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository $entityRelationRepository
     */
    public function setEntityRelationRepository($entityRelationRepository)
    {
        $this->entityRelationRepository = $entityRelationRepository;
    }

    /**
     *
     * @param integer[] $entities
     * @param integer $right
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function hasRight($entities, $right, WorkspaceInterface $workspaceImplementation)
    {
        return $this->getEntityRelationRepository()->findEntitiesWithRight($entities, $right, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param integer[] $selectedEntityTypeIdentifiers
     * @param integer $right
     * @return boolean
     */
    public function setEntityRelations(Workspace $workspace, $selectedEntityTypeIdentifiers, $right)
    {
        foreach ($selectedEntityTypeIdentifiers as $entityType => $entityIdentifiers)
        {
            foreach ($entityIdentifiers as $entityIdentifier)
            {
                $entityRelation = $this->getEntityRelationForWorkspaceEntityTypeAndIdentifier(
                    $workspace, 
                    $entityType, 
                    $entityIdentifier);
                
                if ($entityRelation instanceof WorkspaceEntityRelation)
                {
                    $success = $this->updateEntityRelation(
                        $entityRelation, 
                        $workspace->getId(), 
                        $entityType, 
                        $entityIdentifier, 
                        $right);
                }
                else
                {
                    $success = $this->createEntityRelation($workspace->getId(), $entityType, $entityIdentifier, $right);
                }
                
                if (! $success)
                {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     *
     * @param integer $workspaceId
     * @param integer $entityType
     * @param integer $entityIdentifier
     * @param integer $right
     * @return boolean
     */
    public function createEntityRelation($workspaceId, $entityType, $entityIdentifier, $right)
    {
        $workspaceEntityRelation = new WorkspaceEntityRelation();
        $this->setEntityRelationProperties(
            $workspaceEntityRelation, 
            $workspaceId, 
            $entityType, 
            $entityIdentifier, 
            $right);
        
        if (! $workspaceEntityRelation->create())
        {
            return false;
        }
        
        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation $workspaceEntityRelation
     * @param integer $workspaceId
     * @param integer $entityType
     * @param integer $entityIdentifier
     * @param integer $right
     * @return boolean
     */
    public function updateEntityRelation(WorkspaceEntityRelation $workspaceEntityRelation, $workspaceId, $entityType, 
        $entityIdentifier, $right)
    {
        $this->setEntityRelationProperties(
            $workspaceEntityRelation, 
            $workspaceId, 
            $entityType, 
            $entityIdentifier, 
            $right);
        
        if (! $workspaceEntityRelation->update())
        {
            return false;
        }
        
        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation $workspaceEntityRelation
     * @param integer $workspaceId
     * @param integer $entityType
     * @param integer $entityIdentifier
     * @param integer $right
     */
    private function setEntityRelationProperties(WorkspaceEntityRelation $workspaceEntityRelation, $workspaceId, 
        $entityType, $entityIdentifier, $right)
    {
        $workspaceEntityRelation->set_workspace_id($workspaceId);
        $workspaceEntityRelation->set_entity_type($entityType);
        $workspaceEntityRelation->set_entity_id($entityIdentifier);
        $workspaceEntityRelation->set_rights($right);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param integer $entityType
     * @param integer $entityIdentifier
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation
     */
    public function getEntityRelationForWorkspaceEntityTypeAndIdentifier(Workspace $workspace, $entityType, 
        $entityIdentifier)
    {
        return $this->getEntityRelationRepository()->findEntityRelationForWorkspaceEntityTypeAndIdentifier(
            $workspace, 
            $entityType, 
            $entityIdentifier);
    }

    /**
     *
     * @param integer $identifier
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation
     */
    public function getEntityRelationByIdentifier($identifier)
    {
        return $this->getEntityRelationRepository()->findEntityRelationByIdentifier($identifier);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceEntityRelation $entityRelation
     * @return boolean
     */
    public function deleteEntityRelation(WorkspaceEntityRelation $entityRelation)
    {
        if (! $entityRelation->delete())
        {
            return false;
        }
        
        return true;
    }
}