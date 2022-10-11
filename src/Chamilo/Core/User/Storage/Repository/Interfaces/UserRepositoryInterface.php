<?php
namespace Chamilo\Core\User\Storage\Repository\Interfaces;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface for the user repository
 *
 * @package user
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface UserRepositoryInterface
{

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countUsers(Condition $condition = null);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function createUser(User $user);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     *
     * @return bool
     */
    public function createUserSetting(UserSetting $userSetting);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function deleteUser(User $user);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     *
     * @return bool
     */
    public function deleteUserSetting(UserSetting $userSetting);

    /**
     *
     * @param int $status
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findActiveUsersByStatus($status);

    /**
     * Finds a user by a given email
     *
     * @param string $email
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User;
     */
    public function findUserByEmail($email);

    /**
     * Finds a user by a given id
     *
     * @param int $id
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByIdentifier($id);

    /**
     *
     * @param string $officialCode
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByOfficialCode($officialCode);

    /**
     *
     * @param string $securityToken
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserBySecurityToken($securityToken);

    /**
     * Finds a user by a given username
     *
     * @param string $username
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByUsername($username);

    /**
     *
     * @param string $usernameOrEmail
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail);

    /**
     * Finds a user by a list of parameters
     *
     * @param ?Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     */
    public function findUsers(?Condition $condition, int $count = null, int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection;

    /**
     *
     * @param int[] $userIdentifiers
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsersByIdentifiersOrderedByName($userIdentifiers);

    /**
     *
     * @param \Chamilo\Configuration\Storage\DataClass\Setting $setting
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Core\User\Storage\DataClass\UserSetting
     */
    public function getUserSettingForSettingAndUser(Setting $setting, User $user);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function updateUser(User $user);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     *
     * @return bool
     */
    public function updateUserSetting(UserSetting $userSetting);
}