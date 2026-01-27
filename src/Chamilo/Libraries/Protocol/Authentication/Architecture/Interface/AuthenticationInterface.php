<?php
namespace Chamilo\Libraries\Protocol\Authentication\Architecture\Interface;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Libraries\Authentication
 */
interface AuthenticationInterface
{
    /**
     * Returns the priority of the authentication, lower priorities come first
     */
    public function getPriority(): int;

    public function login(): ?User;

    public function logout(User $user): void;
}