<?php

namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Core\User\Storage\DataClass\UserInvite;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserInviteRepository
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected $dataClassRepository;

    /**
     * UserInviteRepository constructor.
     *
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
     */
    public function __construct(
        \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository $dataClassRepository
    )
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserInvite $userInvite
     *
     * @return bool
     */
    public function createUserInvite(UserInvite $userInvite)
    {
        return $this->dataClassRepository->create($userInvite);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserInvite $userInvite
     *
     * @return bool
     */
    public function updateUserInvite(UserInvite $userInvite)
    {
        return $this->dataClassRepository->update($userInvite);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserInvite $userInvite
     *
     * @return bool
     */
    public function deleteUserInvite(UserInvite $userInvite)
    {
        return $this->dataClassRepository->delete($userInvite);
    }

    /**
     * @param string $securityKey
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass | UserInvite
     */
    public function getUserInviteBySecurityKey(string $securityKey)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserInvite::class, UserInvite::PROPERTY_SECURITY_KEY),
            new StaticConditionVariable($securityKey)
        );

        return $this->dataClassRepository->retrieve(UserInvite::class, new DataClassRetrieveParameters($condition));
    }
}