<?php
namespace Chamilo\Libraries\Protocol\Authentication\Architecture\Interface;

use Chamilo\Core\User\Storage\Entity\User;

/**
 * @package Chamilo\Libraries\Protocol\Authentication\Architecture\Interface
 */
interface AuthenticationInterface
{
    /**
     * Returns the priority of the authentication, lower priorities come first
     */
    public function getPriority(): int;

    public function login(bool $checkIfAuthenticationSourceIsEnabled = true): ?User;

    public function logout(User $user): void;
}