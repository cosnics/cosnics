<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsService
{
    public const RIGHT_ADD = 2;
    public const RIGHT_COPY = 32;
    public const RIGHT_DELETE = 8;
    public const RIGHT_EDIT = 4;
    public const RIGHT_MANAGE = 64;
    public const RIGHT_USE = 16;
    public const RIGHT_VIEW = 1;

    private static ?RightsService $instance = null;

    private ContentObjectRelationService $contentObjectRelationService;

    private EntityRelationService $entityRelationService;

    private EntityService $entityService;

    private WorkspaceService $workspaceService;

    public function __construct(
        ContentObjectRelationService $contentObjectRelationService, EntityRelationService $entityRelationService,
        EntityService $entityService, WorkspaceService $workspaceService
    )
    {
        $this->contentObjectRelationService = $contentObjectRelationService;
        $this->entityRelationService = $entityRelationService;
        $this->entityService = $entityService;
        $this->workspaceService = $workspaceService;
    }

    public function canAddContentObjects(User $user, WorkspaceInterface $workspaceImplementation): bool
    {
        return $this->hasRightForWorkspace(self::RIGHT_ADD, $user, $workspaceImplementation);
    }

    public function canCopyContentObject(
        User $user, ContentObject $contentObject, ?WorkspaceInterface $workspaceImplementation = null
    ): bool
    {
        return $this->hasRightForContentObject(self::RIGHT_COPY, $user, $contentObject, $workspaceImplementation);
    }

    public function canCopyContentObjects(User $user, WorkspaceInterface $workspaceImplementation): bool
    {
        return $this->hasRightForWorkspace(self::RIGHT_COPY, $user, $workspaceImplementation);
    }

    public function canDeleteContentObject(
        User $user, ContentObject $contentObject, ?WorkspaceInterface $workspaceImplementation = null
    ): bool
    {
        return $this->hasRightForContentObject(self::RIGHT_DELETE, $user, $contentObject, $workspaceImplementation);
    }

    public function canDeleteContentObjects(User $user, WorkspaceInterface $workspaceImplementation): bool
    {
        return $this->hasRightForWorkspace(self::RIGHT_DELETE, $user, $workspaceImplementation);
    }

    /**
     * To destroy a content object is to remove it or it's links permanently without an, as of yet, easy way to restore
     * the previous situation.
     * As such it should currently be applied to actions (or their derivatives) which only the object's owner can
     * execute: moving to the recycle bin, unlinking, restoring from the recycle bin, permanent deletion and/or deleting
     * links with other content objects (outside the context of a display or builder)
     */
    public function canDestroyContentObject(
        User $user, ContentObject $contentObject, ?WorkspaceInterface $workspaceImplementation = null
    ): bool
    {
        if ($workspaceImplementation && !$workspaceImplementation instanceof PersonalWorkspace)
        {
            return false;
        }
        else
        {
            return $this->hasContentObjectOwnerRights($user, $contentObject);
        }
    }

    public function canEditContentObject(
        User $user, ContentObject $contentObject, ?WorkspaceInterface $workspaceImplementation = null
    ): bool
    {
        return $this->hasRightForContentObject(self::RIGHT_EDIT, $user, $contentObject, $workspaceImplementation);
    }

    public function canEditContentObjects(User $user, WorkspaceInterface $workspaceImplementation): bool
    {
        return $this->hasRightForWorkspace(self::RIGHT_EDIT, $user, $workspaceImplementation);
    }

    public function canManageWorkspace(User $user, WorkspaceInterface $workspaceImplementation): bool
    {
        return $this->hasRightForWorkspace(self::RIGHT_MANAGE, $user, $workspaceImplementation);
    }

    public function canUseContentObject(
        User $user, ContentObject $contentObject, ?WorkspaceInterface $workspaceImplementation = null
    ): bool
    {
        return $this->hasRightForContentObject(self::RIGHT_USE, $user, $contentObject, $workspaceImplementation);
    }

    public function canUseContentObjects(User $user, WorkspaceInterface $workspaceImplementation): bool
    {
        return $this->hasRightForWorkspace(self::RIGHT_USE, $user, $workspaceImplementation);
    }

    public function canViewContentObject(
        User $user, ContentObject $contentObject, ?WorkspaceInterface $workspaceImplementation = null
    ): bool
    {
        return $this->hasRightForContentObject(self::RIGHT_VIEW, $user, $contentObject, $workspaceImplementation);
    }

    public function canViewContentObjects(User $user, WorkspaceInterface $workspaceImplementation): bool
    {
        return $this->hasRightForWorkspace(self::RIGHT_VIEW, $user, $workspaceImplementation);
    }

    public function getAggregatedRight(int $viewRight, int $useRight, int $copyRight, int $manageRight): int
    {
        $right = $viewRight;

        if ($useRight)
        {
            $right = $right | $useRight;
        }

        if ($copyRight)
        {
            $right = $right | $copyRight;
        }

        if ($manageRight)
        {
            $right = $right | $manageRight;
        }

        return $right;
    }

    public function getContentObjectRelationService(): ContentObjectRelationService
    {
        return $this->contentObjectRelationService;
    }

    public function getEntityRelationService(): EntityRelationService
    {
        return $this->entityRelationService;
    }

    public function getEntityService(): EntityService
    {
        return $this->entityService;
    }

    public function getWorkspaceService(): WorkspaceService
    {
        return $this->workspaceService;
    }

    public function hasContentObjectOwnerRights(User $user, ContentObject $contentObject): bool
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

        return false;
    }

    private function hasRightForContentObject(
        int $right, User $user, ContentObject $contentObject, ?WorkspaceInterface $workspaceImplementation = null
    ): bool
    {
        if ($this->hasContentObjectOwnerRights($user, $contentObject))
        {
            return true;
        }

        // Check if there is actually a workspaceImplementation
        if ($workspaceImplementation instanceof WorkspaceInterface)
        {
            // Check if the content object is in the workspace
            if ($this->getContentObjectRelationService()->isContentObjectInWorkspace(
                $contentObject, $workspaceImplementation
            ))
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
            $contentObjectWorkspaces = $this->getContentObjectRelationService()->getWorkspacesForContentObject(
                $this->getWorkspaceService(), $contentObject
            );

            foreach ($contentObjectWorkspaces as $contentObjectWorkspace)
            {
                if ($this->hasRightForWorkspace($right, $user, $contentObjectWorkspace))
                {
                    return true;
                }
            }

            return false;
        }
    }

    private function hasRightForWorkspace(int $right, User $user, WorkspaceInterface $workspaceImplementation): bool
    {
        if ($this->hasWorkspaceCreatorRights($user, $workspaceImplementation))
        {
            return true;
        }

        return $this->getEntityRelationService()->hasRight(
            $this->getEntityService()->getEntitiesForUser($user), $right, $workspaceImplementation
        );
    }

    public function hasWorkspaceCreatorRights(User $user, WorkspaceInterface $workspaceImplementation): bool
    {
        // Check if the user is a platform administrator
        if ($user->is_platform_admin())
        {
            return true;
        }

        if ($this->isWorkspaceCreator($user, $workspaceImplementation))
        {
            return true;
        }

        return $this->getEntityRelationService()->hasRight(
            $this->getEntityService()->getEntitiesForUser($user), self::RIGHT_MANAGE, $workspaceImplementation
        );
    }

    public function isContentObjectOwner(User $user, ContentObject $contentObject): bool
    {
        return $user->getId() == $contentObject->get_owner_id();
    }

    public function isWorkspaceCreator(User $user, WorkspaceInterface $workspaceImplementation): bool
    {
        return $user->getId() == $workspaceImplementation->getCreatorId();
    }

    public function isWorkspaceCreatorByWorkspaceIdentifier(User $user, string $workspaceIdentifier): bool
    {
        $workspace = $this->getWorkspaceService()->getWorkspaceByIdentifier($workspaceIdentifier);

        return $this->isWorkspaceCreator($user, $workspace);
    }
}