<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Libraries\Authentication
 */
interface AuthenticationInterface
{
    public function getAuthenticationType(): string;

    /**
     * Returns the priority of the authentication, lower priorities come first
     */
    public function getPriority(): int;

    public function login(): ?User;

    public function logout(User $user): void;
}