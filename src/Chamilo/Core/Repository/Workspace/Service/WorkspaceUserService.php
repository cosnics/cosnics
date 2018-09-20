<?php

namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Service\UserService;

/**
 * @package Chamilo\Core\Repository\Workspace\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class WorkspaceUserService
{
    /**
     * @var \Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository
     */
    protected $entityRelationRepository;

    /**
     * @var \Chamilo\Core\Group\Service\GroupService
     */
    protected $groupService;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * WorkspaceUserService constructor.
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository $entityRelationRepository
     * @param \Chamilo\Core\Group\Service\GroupService $groupService
     * @param \Chamilo\Core\User\Service\UserService $userService
     */
    public function __construct(
        EntityRelationRepository $entityRelationRepository, GroupService $groupService, UserService $userService
    )
    {
        $this->entityRelationRepository = $entityRelationRepository;
        $this->groupService = $groupService;
        $this->userService = $userService;
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function getAllUsersInWorkspace(Workspace $workspace)
    {
        if($workspace instanceof PersonalWorkspace)
        {
            return $workspace->getOwner();
        }

        $userIds = [];

        $entityRelations = $this->entityRelationRepository->findEntityRelationsForWorkspace($workspace);
        foreach($entityRelations as $entityRelation)
        {
            $entityType = $entityRelation->get_entity_type();

            if($entityType == UserEntity::ENTITY_TYPE)
            {
                $userIds[] = $entityRelation->get_entity_id();
            }
            elseif($entityType == PlatformGroupEntity::ENTITY_TYPE)
            {
                $group = $this->groupService->getGroupByIdentifier($entityRelation->get_entity_id());
                $userIds = array_merge($userIds, $group->get_users(true, true));
            }
        }

        $workspaceUsers = $this->userService->findUsersByIdentifiers($userIds);
        $workspaceUsers[] = $workspace->getCreator();

        return $workspaceUsers;
    }
}