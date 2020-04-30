<?php
namespace Chamilo\Core\Repository\Workspace\Favourite\Repository;

use Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Favourite\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FavouriteRepository
{

    /**
     *
     * @param integer $identifier
     * @return \Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite
     */
    public function findFavouriteByIdentifier($identifier)
    {
        return DataManager::retrieve_by_id(WorkspaceUserFavourite::class, $identifier);
    }

    /**
     *
     * @param User $user
     * @param integer $workspaceIdentifier
     * @return \Chamilo\Core\Repository\Workspace\Favourite\Storage\DataClass\WorkspaceUserFavourite
     */
    public function findWorkspaceUserFavouriteByUserAndWorkspaceIdentifier(User $user, $workspaceIdentifier)
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceUserFavourite::class,
                WorkspaceUserFavourite::PROPERTY_USER_ID), 
            new StaticConditionVariable($user->getId()));
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                WorkspaceUserFavourite::class,
                WorkspaceUserFavourite::PROPERTY_WORKSPACE_ID), 
            new StaticConditionVariable($workspaceIdentifier));
        
        $condition = new AndCondition($conditions);
        
        return DataManager::retrieve(WorkspaceUserFavourite::class, new DataClassRetrieveParameters($condition));
    }
}