<?php
namespace Chamilo\Core\Repository\Workspace\Repository;

use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Repository
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceRepository
{

    /**
     *
     * @param integer $identifier
     * @return \Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace
     */
    public function findWorkspaceByIdentifier($identifier)
    {
        return DataManager :: retrieve_by_id(Workspace :: class_name(), $identifier);
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param integer $limit
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     * @return \Chamilo\Libraries\Storage\ResultSet\ResultSet
     */
    public function findWorkspacesByCreator(User $user, $limit, $offset, $orderProperty = array())
    {
        return DataManager :: retrieves(
            Workspace :: class_name(),
            new DataClassRetrievesParameters(
                $this->getWorkspacesByCreatorCondition($user, $limit, $offset, $orderProperty)));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return integer
     */
    public function countWorkspacesByCreator(User $user)
    {
        return DataManager :: count(
            Workspace :: class_name(),
            new DataClassCountParameters($this->getWorkspacesByCreatorCondition($user)));
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Libraries\Storage\Query\Condition\EqualityCondition
     */
    private function getWorkspacesByCreatorCondition(User $user)
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Workspace :: class_name(), Workspace :: PROPERTY_CREATOR_ID),
            new StaticConditionVariable($user->getId()));
    }
}