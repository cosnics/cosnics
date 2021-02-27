<?php

namespace Chamilo\Core\User\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\Repository\CommonDataClassRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserInvite;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Join;
use Chamilo\Libraries\Storage\Query\Joins;
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

    /**
     * @param int $userInviteId
     *
     * @return UserInvite|\Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass|false
     */
    public function getUserInviteById(int $userInviteId)
    {
        return $this->dataClassRepository->retrieveById(UserInvite::class, $userInviteId);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     */
    public function getUserInvitesFromUser(User $user)
    {
        $properties = new DataClassProperties(
            [
                new PropertyConditionVariable(User::class, User::PROPERTY_EMAIL),
                new PropertyConditionVariable(UserInvite::class, UserInvite::PROPERTY_ID),
                new PropertyConditionVariable(UserInvite::class, UserInvite::PROPERTY_STATUS),
                new PropertyConditionVariable(UserInvite::class, UserInvite::PROPERTY_VALID_UNTIL),
            ]
        );

        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserInvite::class, UserInvite::PROPERTY_INVITED_BY_USER_ID),
            new StaticConditionVariable($user->getId())
        );

        $joins = new Joins();

        $joins->add(
            new Join(
                User::class, new EqualityCondition(
                    new PropertyConditionVariable(User::class, User::PROPERTY_ID),
                    new PropertyConditionVariable(UserInvite::class, UserInvite::PROPERTY_USER_ID)
                )
            )
        );

        return $this->dataClassRepository->records(
            UserInvite::class, new RecordRetrievesParameters($properties, $condition, null, null, [], $joins)
        );
    }
}
