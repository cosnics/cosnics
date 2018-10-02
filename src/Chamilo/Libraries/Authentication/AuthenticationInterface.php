<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface AuthenticationInterface
 *
 * @package Chamilo\Libraries\Authentication
 */
interface AuthenticationInterface
{
    /**
     * @return User
     */
    public function login();

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function logout(User $user);
}