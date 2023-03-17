<?php

namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Group\Integration\Chamilo\Libraries\Rights\Service\GroupEntityProvider;
use Chamilo\Core\Group\Service\GroupService;
use Chamilo\Core\Group\Service\GroupsTreeTraverser;
use Chamilo\Core\Repository\Workspace\Repository\EntityRelationRepository;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Integration\Chamilo\Libraries\Rights\Service\UserEntityProvider;
use Chamilo\Core\User\Service\UserService;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WorkspaceUserService
{
    protected EntityRelationRepository $entityRelationRepository;

    protected GroupService $groupService;

    protected GroupsTreeTraverser $groupsTreeTraverser;

    protected UserService $userService;

    public function __construct(
        EntityRelationRepository $entityRelationRepository, GroupService $groupService,
        GroupsTreeTraverser $groupsTreeTraverser, UserService $userService
    )
    {
        $this->entityRelationRepository = $entityRelationRepository;
        $this->groupService = $groupService;
        $this->userService = $userService;
        $this->groupsTreeTraverser = $groupsTreeTraverser;
    }

    /**
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User[]>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function getAllUsersInWorkspace(Workspace $workspace): ArrayCollection
    {
        $userIds = [];

        $userIds[] = $workspace->getCreatorId();

        $entityRelations = $this->getEntityRelationRepository()->findEntityRelationsForWorkspace($workspace);

        foreach ($entityRelations as $entityRelation)
        {
            $entityType = $entityRelation->get_entity_type();

            if ($entityType == UserEntityProvider::ENTITY_TYPE)
            {
                $userIds[] = $entityRelation->get_entity_id();
            }
            elseif ($entityType == GroupEntityProvider::ENTITY_TYPE)
            {
                $group = $this->getGroupService()->findGroupByIdentifier($entityRelation->get_entity_id());
                $groupUserIdentifiers =
                    $this->getGroupsTreeTraverser()->findUserIdentifiersForGroup($group, true, true);

                $userIds = array_merge($userIds, $groupUserIdentifiers);
            }
        }

        return $this->getUserService()->findUsersByIdentifiers($userIds);
    }

    public function getEntityRelationRepository(): EntityRelationRepository
    {
        return $this->entityRelationRepository;
    }

    public function getGroupService(): GroupService
    {
        return $this->groupService;
    }

    public function getGroupsTreeTraverser(): GroupsTreeTraverser
    {
        return $this->groupsTreeTraverser;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }
}