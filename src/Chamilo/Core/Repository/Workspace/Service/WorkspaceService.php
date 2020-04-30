<?php
namespace Chamilo\Core\Repository\Workspace\Service;

use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\Rights\Entity\PlatformGroupEntity;
use Chamilo\Core\Rights\Entity\UserEntity;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use InvalidArgumentException;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceService
{
    const TYPE_PERSONAL = 1;
    const TYPE_WORKSPACE = 2;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository
     */
    private $workspaceRepository;

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository $workspaceRepository
     */
    public function __construct(WorkspaceRepository $workspaceRepository)
    {
        $this->workspaceRepository = $workspaceRepository;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository
     */
    public function getWorkspaceRepository()
    {
        return $this->workspaceRepository;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Repository\WorkspaceRepository $workspaceRepository
     */
    public function setWorkspaceRepository($workspaceRepository)
    {
        $this->workspaceRepository = $workspaceRepository;
    }

    /**
     *
     * @param integer $identifier
     *
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function getWorkspaceByIdentifier($identifier)
    {
        return $this->getWorkspaceRepository()->findWorkspaceByIdentifier($identifier);
    }

    /**
     *
     * @param integer[] $identifiers
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getWorkspacesByIdentifiers($identifiers)
    {
        return $this->getWorkspaceRepository()->findWorkspacesByIdentifiers($identifiers);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $identifier
     *
     * @return \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    public function determineWorkspaceForUserByIdentifier(User $user, $identifier = null)
    {
        if (! empty($identifier))
        {
            if (! is_numeric($identifier))
            {
                throw new InvalidArgumentException();
            }
            
            return $this->getWorkspaceByIdentifier($identifier);
        }
        else
        {
            return $this->getPersonalWorkspaceForUser($user);
        }
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\Repository\Workspace\PersonalWorkspace
     */
    public function getPersonalWorkspaceForUser(User $user)
    {
        return new PersonalWorkspace($user);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getWorkspacesByCreator(User $user, $limit, $offset, $orderProperty = null)
    {
        return $this->getWorkspaceRepository()->findWorkspacesByCreator($user, $limit, $offset, $orderProperty);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param $right
     * @param $limit
     * @param $offset
     * @param null $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getWorkspacesForUser(User $user, $right = RightsService :: RIGHT_VIEW, $limit = null, $offset = null, $orderProperty = null)
    {
        return $this->getWorkspaceRepository()->findWorkspacesForUser(
            $user, 
            $this->getEntitiesForUser($user), 
            $right, 
            $limit, 
            $offset, 
            $orderProperty);
    }

    /**
     * Finds a list of workspace to which a given user has right with the possibility to exclude workspaces
     * based on their identifiers
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param $right
     * @param array $excludedWorkspaceIdentifiers
     * @param $limit
     * @param $offset
     * @param null $orderProperty
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getWorkspacesForUserWithExcludedWorkspaces(User $user, $right = RightsService :: RIGHT_VIEW, 
        $excludedWorkspaceIdentifiers = array(), $limit, $offset, $orderProperty = null)
    {
        return $this->getWorkspaceRepository()->findWorkspacesForUserWithExcludedWorkspaces(
            $user, 
            $this->getEntitiesForUser($user), 
            $right, 
            $excludedWorkspaceIdentifiers, 
            $limit, 
            $offset, 
            $orderProperty);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     */
    public function countWorkspacesForUser(User $user, $right = RightsService :: RIGHT_VIEW)
    {
        return $this->getWorkspaceRepository()->countWorkspacesForUser($user, $this->getEntitiesForUser($user), $right);
    }

    /**
     * Counts the number of workspaces to which a given user has right with the possibility to exclude workspaces
     * based on their identifiers
     * 
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $right
     * @param int[] $excludedWorkspaceIdentifiers
     *
     * @return int
     */
    public function countWorkspacesForUserWithExcludedWorkspaces(User $user, $right = RightsService :: RIGHT_VIEW, 
        $excludedWorkspaceIdentifiers = array())
    {
        return $this->getWorkspaceRepository()->countWorkspacesForUserWithExcludedWorkspaces(
            $user, 
            $this->getEntitiesForUser($user), 
            $right, 
            $excludedWorkspaceIdentifiers);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     */
    public function countWorkspacesByCreator(User $user)
    {
        return $this->getWorkspaceRepository()->countWorkspacesByCreator($user);
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getAllWorkspaces()
    {
        return $this->getWorkspaceRepository()->findAllWorkspaces();
    }

    /**
     *
     * @return integer
     */
    public function countAllWorkspaces()
    {
        return $this->getWorkspaceRepository()->countAllWorkspaces();
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getSharedWorkspacesForUser(User $user, $limit, $offset, $orderProperty = null)
    {
        return $this->getWorkspaceRepository()->findSharedWorkspacesForEntities(
            $this->getEntitiesForUser($user), 
            $limit, 
            $offset, 
            $orderProperty);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     */
    public function countSharedWorkspacesForUser(User $user)
    {
        return $this->getWorkspaceRepository()->countSharedWorkspacesForEntities($this->getEntitiesForUser($user));
    }

    /**
     *
     * @param User $user
     *
     * @return integer[]
     */
    public function getEntitiesForUser(User $user)
    {
        $entities = array();
        
        $entities[UserEntity::ENTITY_TYPE] = array($user->get_id());
        $entities[PlatformGroupEntity::ENTITY_TYPE] = array();
        
        $userGroupIdentifiers = $user->get_groups(true);
        
        foreach ($userGroupIdentifiers as $userGroupIdentifier)
        {
            $entities[PlatformGroupEntity::ENTITY_TYPE][] = $userGroupIdentifier;
        }
        
        return $entities;
    }

    /**
     *
     * @param string[] $workspaceProperties
     *
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function createWorkspace($workspaceProperties)
    {
        $workspace = new Workspace();
        $this->setWorkspaceProperties($workspace, $workspaceProperties);
        
        if (! $workspace->create())
        {
            return false;
        }
        
        return $workspace;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param string[] $workspaceProperties
     *
     * @return boolean
     */
    public function updateWorkspace(Workspace $workspace, $workspaceProperties)
    {
        $this->setWorkspaceProperties($workspace, $workspaceProperties);
        
        if (! $workspace->update())
        {
            return false;
        }
        
        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     *
     * @return boolean
     */
    public function deleteWorkspace(Workspace $workspace)
    {
        if (! $workspace->delete())
        {
            return false;
        }
        
        return true;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace $workspace
     * @param string[] $workspaceProperties
     */
    private function setWorkspaceProperties(Workspace $workspace, $workspaceProperties)
    {
        $workspace->setName($workspaceProperties[Workspace::PROPERTY_NAME]);
        $workspace->setDescription($workspaceProperties[Workspace::PROPERTY_DESCRIPTION]);
        $workspace->setCreationDate($workspaceProperties[Workspace::PROPERTY_CREATION_DATE]);
        $workspace->setCreatorId($workspaceProperties[Workspace::PROPERTY_CREATOR_ID]);
    }

    /**
     *
     * @param EntityService $entityService
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\ResultSet\DataClassResultSet
     */
    public function getWorkspaceFavouritesByUser(EntityService $entityService, User $user, $limit = null, $offset = null, 
        $orderProperty = null)
    {
        if (is_null($orderProperty))
        {
            $orderProperty = array(
                new OrderBy(new PropertyConditionVariable(Workspace::class, Workspace::PROPERTY_NAME), SORT_ASC));
        }
        
        return $this->getWorkspaceRepository()->findWorkspaceFavouritesByUser(
            $user, 
            $entityService->getEntitiesForUser($user), 
            $limit, 
            $offset, 
            $orderProperty);
    }

    /**
     *
     * @param EntityService $entityService
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return integer
     */
    public function countWorkspaceFavouritesByUser(EntityService $entityService, User $user)
    {
        return $this->getWorkspaceRepository()->countWorkspaceFavouritesByUser(
            $user, 
            $entityService->getEntitiesForUser($user));
    }

    /**
     *
     * @param integer $type
     * @param integer $typeIdentifier
     *
     * @return \Chamilo\Core\Repository\Workspace\PersonalWorkspace|\Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function getWorkspaceByTypeAndTypeIdentifier($type, $typeIdentifier)
    {
        if ($type == self::TYPE_PERSONAL)
        {
            $user = DataManager::retrieve_by_id(User::class, $typeIdentifier);
            
            return $this->getPersonalWorkspaceForUser($user);
        }
        else
        {
            return $this->getWorkspaceByIdentifier($typeIdentifier);
        }
    }
}