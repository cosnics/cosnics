<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsService
{
    const RIGHT_VIEW = 1;
    const RIGHT_ADD = 2;
    const RIGHT_EDIT = 4;
    const RIGHT_DELETE = 8;
    const RIGHT_USE = 16;
    const RIGHT_COPY = 32;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService
     */
    private $contentObjectRelationService;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Service\EntityRelationService
     */
    private $entityRelationService;

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService $contentObjectRelationService
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     */
    public function __construct(ContentObjectRelationService $contentObjectRelationService, 
        EntityRelationService $entityRelationService)
    {
        $this->contentObjectRelationService = $contentObjectRelationService;
        $this->entityRelationService = $entityRelationService;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService
     */
    public function getContentObjectRelationService()
    {
        return $this->contentObjectRelationService;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Service $contentObjectRelationService
     */
    public function setContentObjectRelationService($contentObjectRelationService)
    {
        $this->contentObjectRelationService = $contentObjectRelationService;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Service\EntityRelationService
     */
    public function getEntityRelationService()
    {
        return $this->entityRelationService;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Service\EntityRelationService $entityRelationService
     */
    public function setEntityRelationService($entityRelationService)
    {
        $this->entityRelationService = $entityRelationService;
    }

    /**
     *
     * @param integer $right
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    private function hasRightForWorkspace($right, User $user, WorkspaceInterface $workspaceImplementation)
    {
        // Check if the user is a platform administrator
        if ($user->is_platform_admin())
        {
            return true;
        }
        
        if ($this->isWorkspaceImplementationCreator($user, $workspaceImplementation))
        {
            return true;
        }
        
        return $this->getEntityRelationService()->hasRight($user, $right, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function isWorkspaceImplementationCreator(User $user, WorkspaceInterface $workspaceImplementation)
    {
        return $user->getId() == $workspaceImplementation->getCreatorId();
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function canViewContentObjects(User $user, WorkspaceInterface $workspaceImplementation)
    {
        return $this->hasRightForWorkspace(self :: RIGHT_VIEW, $user, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function canAddContentObjects(User $user, WorkspaceInterface $workspaceImplementation)
    {
        return $this->hasRightForWorkspace(self :: RIGHT_ADD, $user, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function canEditContentObjects(User $user, WorkspaceInterface $workspaceImplementation)
    {
        return $this->hasRightForWorkspace(self :: RIGHT_EDIT, $user, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function canDeleteContentObjects(User $user, WorkspaceInterface $workspaceImplementation)
    {
        return $this->hasRightForWorkspace(self :: RIGHT_DELETE, $user, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function canUseContentObjects(User $user, WorkspaceInterface $workspaceImplementation)
    {
        return $this->hasRightForWorkspace(self :: RIGHT_USE, $user, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function canCopyContentObjects(User $user, WorkspaceInterface $workspaceImplementation)
    {
        return $this->hasRightForWorkspace(self :: RIGHT_COPY, $user, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function canViewContentObject(User $user, ContentObject $contentObject, 
        WorkspaceInterface $workspaceImplementation = null)
    {
        return $this->hasRightForContentObject(self :: RIGHT_VIEW, $contentObject, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function canEditContentObject(User $user, ContentObject $contentObject, 
        WorkspaceInterface $workspaceImplementation = null)
    {
        return $this->hasRightForContentObject(self :: RIGHT_EDIT, $contentObject, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function canDeleteContentObject(User $user, ContentObject $contentObject, 
        WorkspaceInterface $workspaceImplementation = null)
    {
        return $this->hasRightForContentObject(self :: RIGHT_DELETE, $contentObject, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function canUseContentObject(User $user, ContentObject $contentObject, 
        WorkspaceInterface $workspaceImplementation = null)
    {
        return $this->hasRightForContentObject(self :: RIGHT_USE, $contentObject, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    public function canCopyContentObject(User $user, ContentObject $contentObject, 
        WorkspaceInterface $workspaceImplementation = null)
    {
        return $this->hasRightForContentObject(self :: RIGHT_COPY, $contentObject, $workspaceImplementation);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspaceImplementation
     *
     * @return boolean
     */
    private function hasRightForContentObject(User $user, $right, ContentObject $contentObject, 
        WorkspaceInterface $workspaceImplementation = null)
    {
        // Check if the user is a platform administrator
        if ($user->is_platform_admin())
        {
            return true;
        }
        
        // Check if the user is also the owner of the content object
        if ($this->isContentObjectOwner($user, $contentObject))
        {
            return true;
        }
        
        // Check if there is actually a workspaceImplementation
        if ($workspaceImplementation instanceof WorkspaceInterface)
        {
            // Check if the content object is in the workspace
            if ($this->getContentObjectRelationService()->isContentObjectInWorkspace(
                $contentObject, 
                $workspaceImplementation))
            {
                // Check if the user has the requested right in the workspace
                return $this->hasRightForWorkspace($right, $user, $workspaceImplementation);
            }
            else
            {
                return false;
            }
        }
        // Is the contentObject in a workspace the user has the requested right for
        else
        {
            return $this->getContentObjectRelationService()->hasRight($user, $right, $contentObject);
        }
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return boolean
     */
    public function isContentObjectOwner(User $user, ContentObject $contentObject)
    {
        return $user->getId() == $contentObject->get_owner_id();
    }
}