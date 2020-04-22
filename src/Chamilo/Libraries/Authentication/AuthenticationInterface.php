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
     * Returns the short name of the authentication to check in the settings
     *
     * @return string
     */
    public function getAuthenticationType();

    /**
     * Returns the priority of the authentication, lower priorities come first
     *
     * @return int
     */
    public function getPriority();

    /**
     * @return User
     */
    public function login();

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     */
    public function logout(User $user);
}