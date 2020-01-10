<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\ResultSet\ResultSet;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectRelationService
{

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository
     */
    private $contentObjectRelationRepository;

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository $contentObjectRelationRepository
     */
    public function __construct(ContentObjectRelationRepository $contentObjectRelationRepository)
    {
        $this->contentObjectRelationRepository = $contentObjectRelationRepository;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository
     */
    public function getContentObjectRelationRepository()
    {
        return $this->contentObjectRelationRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository $contentObjectRelationRepository
     */
    public function setContentObjectRelationRepository($contentObjectRelationRepository)
    {
        $this->contentObjectRelationRepository = $contentObjectRelationRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function isContentObjectInWorkspace(ContentObject $contentObject, WorkspaceInterface $workspaceImplementation)
    {
        return $this->getContentObjectRelationRepository()->findContentObjectInWorkspace(
            $contentObject, 
            $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation
     */
    public function getContentObjectRelationForWorkspaceAndContentObject(Workspace $workspace, 
        ContentObject $contentObject)
    {
        return $this->getContentObjectRelationRepository()->findContentObjectRelationForWorkspaceAndContentObject(
            $workspace, 
            $contentObject);
    }

    /**
     *
     * @param ContentObject $contentObject
     *
     * @return ResultSet
     */
    public function getContentObjectRelationsForContentObject(ContentObject $contentObject)
    {
        return $this->getContentObjectRelationRepository()->findContentObjectRelationsForContentObject($contentObject);
    }

    /**
     * Updates the content object id of all WorkspaceContentObjectRelations based on an old content object id
     * 
     * @param int $oldContentObjectId
     * @param int $newContentObjectId
     */
    public function updateContentObjectIdInAllWorkspaces($oldContentObjectId, $newContentObjectId)
    {
        if (empty($oldContentObjectId))
        {
            throw new \InvalidArgumentException('The given old content object id can not be empty');
        }
        
        if (empty($newContentObjectId))
        {
            throw new \InvalidArgumentException('The given new content object id can not be empty');
        }
        
        $contentObjectRelations = $this->getContentObjectRelationRepository()->findContentObjectRelationsForContentObjectById(
            $oldContentObjectId);
        
        while ($contentObjectRelation = $contentObjectRelations->next_result())
        {
            /** @var WorkspaceContentObjectRelation $contentObjectRelation */
            $contentObjectRelation->setContentObjectId($newContentObjectId);
            if (! $contentObjectRelation->update())
            {
                throw new \RuntimeException(
                    sprintf(
                        'Could not update the WorkspaceContentObjectRelation object with id %s', 
                        $contentObjectRelation->getId()));
            }
        }
    }

    /**
     *
     * @param integer $workspaceId
     * @param integer $contentObjectId
     * @param integer $categoryId
     */
    public function createContentObjectRelation($workspaceId, $contentObjectId, $categoryId)
    {
        $contentObjectRelation = new WorkspaceContentObjectRelation();
        $this->setContentObjectRelationProperties($contentObjectRelation, $workspaceId, $contentObjectId, $categoryId);
        
        if (! $contentObjectRelation->create())
        {
            return false;
        }
        
        return $contentObjectRelation;
    }

    public function addContentObjectsToWorkspace(Workspace $workspace, array $contentObjectIds, int $categoryId = null)
    {
        foreach($contentObjectIds as $contentObjectId)
        {
            $this->createContentObjectRelation($workspace->getId(), $contentObjectId, $categoryId);
        }
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation $contentObjectRelation
     * @param integer $workspaceId
     * @param integer $contentObjectId
     * @param integer $categoryId
     */
    public function updateContentObjectRelation(WorkspaceContentObjectRelation $contentObjectRelation, $workspaceId, 
        $contentObjectId, $categoryId)
    {
        $this->setContentObjectRelationProperties($contentObjectRelation, $workspaceId, $contentObjectId, $categoryId);
        
        if (! $contentObjectRelation->update())
        {
            return false;
        }
        
        return $contentObjectRelation;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation $contentObjectRelation
     * @param integer $workspaceId
     * @param integer $contentObjectId
     * @param integer $categoryId
     */
    private function setContentObjectRelationProperties(WorkspaceContentObjectRelation $contentObjectRelation, 
        $workspaceId, $contentObjectId, $categoryId)
    {
        $contentObjectRelation->setWorkspaceId($workspaceId);
        $contentObjectRelation->setContentObjectId($contentObjectId);
        $contentObjectRelation->setCategoryId($categoryId);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @return boolean
     */
    public function deleteContentObjectRelationByWorkspaceAndContentObjectIdentifier(Workspace $workspace, 
        ContentObject $contentObject)
    {
        $contentObjectRelation = $this->getContentObjectRelationForWorkspaceAndContentObject($workspace, $contentObject);
        
        if (! $contentObjectRelation->delete())
        {
            return false;
        }
        
        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Service\WorkspaceService $workspaceService
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getWorkspacesForContentObject(WorkspaceService $workspaceService, ContentObject $contentObject)
    {
        $workspaceIdentifiers = $this->getWorkspaceIdentifiersForContentObject($contentObject);
        return $workspaceService->getWorkspacesByIdentifiers($workspaceIdentifiers);
    }

    /**
     *
     * @param ContentObject $contentObject
     *
     * @return int
     */
    public function countWorkspacesForContentObject(ContentObject $contentObject)
    {
        return count($this->getWorkspaceIdentifiersForContentObject($contentObject));
    }

    /**
     * Returns the available workspaces in which a given user can add the given content object
     * 
     * @param WorkspaceService $workspaceService
     * @param ContentObject[] $contentObjects
     * @param User $user
     * @param int $limit
     * @param int $offset
     * @param OrderBy $orderBy
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getAvailableWorkspacesForContentObjectsAndUser(WorkspaceService $workspaceService, $contentObjects, 
        User $user, $limit = null, $offset = null, $orderBy = null)
    {
        $workspaceIdentifiers = array();
        
        foreach ($contentObjects as $contentObject)
        {
            $workspaceIdentifiers = array_merge(
                $workspaceIdentifiers, 
                $this->getWorkspaceIdentifiersForContentObject($contentObject));
        }
        
        return $workspaceService->getWorkspacesForUserWithExcludedWorkspaces(
            $user, 
            RightsService::RIGHT_ADD, 
            $workspaceIdentifiers, 
            $limit, 
            $offset, 
            $orderBy);
    }

    /**
     * Returns the available workspaces in which a given user can add the given content object
     * 
     * @param WorkspaceService $workspaceService
     * @param ContentObject[] $contentObjects
     * @param User $user
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function countAvailableWorkspacesForContentObjectsAndUser(WorkspaceService $workspaceService, $contentObjects, 
        User $user)
    {
        $workspaceIdentifiers = array();
        
        foreach ($contentObjects as $contentObject)
        {
            $workspaceIdentifiers = array_merge(
                $workspaceIdentifiers, 
                $this->getWorkspaceIdentifiersForContentObject($contentObject));
        }
        
        return $workspaceService->countWorkspacesForUserWithExcludedWorkspaces(
            $user, 
            RightsService::RIGHT_ADD, 
            $workspaceIdentifiers);
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @return integer[]
     */
    public function getWorkspaceIdentifiersForContentObject(ContentObject $contentObject)
    {
        return $this->getContentObjectRelationRepository()->findWorkspaceIdentifiersForContentObject($contentObject);
    }
}
