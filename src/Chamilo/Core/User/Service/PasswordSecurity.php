<?php

namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @author - Sven Vanpoucke - Hogeschool Gent
 * @package Chamilo\Core\User\Service
 */
class PasswordSecurity
{
    const PASSWORD_ENCRYPTION = PASSWORD_BCRYPT;

    /**
     * @var \Chamilo\Core\User\Storage\Repository\UserRepository
     */
    protected $userRepository;

    /**
     * PasswordSecurity constructor.
     *
     * @param \Chamilo\Core\User\Storage\Repository\UserRepository $userRepository
     */
    public function __construct(\Chamilo\Core\User\Storage\Repository\UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $chosenPassword
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function setPasswordForUser(User $user, string $chosenPassword)
    {
        $user->set_password($this->hashPassword($chosenPassword));

        return $user;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $passwordToVerify
     *
     * @return bool
     */
    public function isPasswordValidForUser(User $user, string $passwordToVerify)
    {
        return $this->isPasswordValid($user->get_password(), $passwordToVerify);
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $plainTextPassword
     */
    public function convertPasswordForUser(User $user, string $plainTextPassword)
    {
        $userPassword = $user->get_password();
        if (strpos($userPassword, '$2y$10$') !== false)
        {
            return;
        }

        $user->set_password($this->hashPassword($plainTextPassword));

        if (!$this->userRepository->update($user))
        {
            throw new \RuntimeException(
                'Could not update the password security for the user %s, will retry at next login attempt'
            );
        }
    }

    /**
     * @param string $password
     *
     * @return string
     */
    protected function hashPassword(string $password)
    {
        $result = password_hash($password, self::PASSWORD_ENCRYPTION);
        if ($result == false)
        {
            throw new \RuntimeException('Could not hash the password with BCRYPT');
        }

        return $result;
    }

    /**
     * @param string $storedPassword
     * @param string $passwordToVerify
     *
     * @return bool
     */
    protected function isPasswordValid(string $storedPassword, string $passwordToVerify)
    {
        return password_verify($passwordToVerify, $storedPassword);
    }
}