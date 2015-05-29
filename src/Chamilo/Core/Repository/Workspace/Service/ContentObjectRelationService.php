<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;

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
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $right
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    public function hasRight(User $user, $right, ContentObject $contentObject)
    {
        return $this->getContentObjectRelationRepository()->findContentObjectForUserWithRight(
            $user, 
            $right, 
            $contentObject);
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
        
        if (! $contentObjectRelation->create())
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
}