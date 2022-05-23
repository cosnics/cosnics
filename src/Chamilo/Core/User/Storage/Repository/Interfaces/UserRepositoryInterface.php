<?php
namespace Chamilo\Core\User\Storage\Repository\Interfaces;

use Chamilo\Configuration\Storage\DataClass\Setting;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataClass\UserSetting;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Interface for the user repository
 *
 * @package user
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface UserRepositoryInterface
{

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
     * @param string $securityToken
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserBySecurityToken($securityToken);

    /**
     *
     * @param string $officialCode
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByOfficialCode($officialCode);

    /**
     * Finds a user by a list of parameters
     *
     * @param Condition $condition
     * @param int $count
     * @param int $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $order_by
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsers(Condition $condition, $count = null, $offset = null, $order_by = null);

    /**
     *
     * @param int[] $userIdentifiers
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsersByIdentifiersOrderedByName($userIdentifiers);

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return integer
     */
    public function countUsers(Condition $condition = null);

    /**
     * Finds a user by a given email
     *
     * @param string $email
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User;
     */
    public function findUserByEmail($email);

    /**
     * Finds a user by a given username
     *
     * @param string $username
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
     *
     * @param integer $status
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findActiveUsersByStatus($status);

    /**
     *
     * @param \Chamilo\Configuration\Storage\DataClass\Setting $setting
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return \Chamilo\Core\User\Storage\DataClass\UserSetting
     */
    public function getUserSettingForSettingAndUser(Setting $setting, User $user);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     * @return boolean
     */
    public function createUserSetting(UserSetting $userSetting);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     * @return boolean
     */
    public function updateUserSetting(UserSetting $userSetting);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     * @return boolean
     */
    public function deleteUserSetting(UserSetting $userSetting);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return boolean
     */
    public function createUser(User $user);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return boolean
     */
    public function updateUser(User $user);

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @return boolean
     */
    public function deleteUser(User $user);
}