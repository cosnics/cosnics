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
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
interface UserRepositoryInterface
{

    public function countUsers(?Condition $condition = null): int;

    public function createUser(User $user): bool;

    public function createUserSetting(UserSetting $userSetting): bool;

    public function deleteUser(User $user): bool;

    public function deleteUserSetting(UserSetting $userSetting): bool;

    /**
     * @param int $status
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     */
    public function findActiveUsersByStatus(int $status): ArrayCollection;

    public function findUserByEmail($email): ?User;

    public function findUserByIdentifier(string $identifier): ?User;

    public function findUserByOfficialCode(string $officialCode): ?User;

    public function findUserBySecurityToken(string $securityToken): ?User;

    public function findUserByUsername(string $username): ?User;

    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?User;

    /**
     * @param ?\Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param ?int $count
     * @param ?int $offset
     * @param ?\Chamilo\Libraries\Storage\Query\OrderBy $orderBy
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     */
    public function findUsers(?Condition $condition, ?int $count = null, ?int $offset = null, ?OrderBy $orderBy = null
    ): ArrayCollection;

    /**
     * @param int[] $userIdentifiers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Core\User\Storage\DataClass\User>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findUsersByIdentifiersOrderedByName(array $userIdentifiers): ArrayCollection;

    public function findUserSettingForSettingAndUser(Setting $setting, User $user): ?UserSetting;

    public function updateUser(User $user): bool;

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\UserSetting $userSetting
     *
     * @return bool
     */
    public function updateUserSetting(UserSetting $userSetting): bool;
}