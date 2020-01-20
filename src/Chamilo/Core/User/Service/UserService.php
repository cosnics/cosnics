<?php

namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Platform\Session\SessionUtilities;

/**
 *
 * @package Chamilo\Core\User\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class UserService
{

    /**
     *
     * @var \Chamilo\Core\User\Storage\Repository\UserRepository
     */
    protected $userRepository;

    /**
     * @var \Chamilo\Core\User\Service\PasswordSecurity
     */
    protected $passwordSecurity;

    /**
     * @var \Chamilo\Libraries\Platform\Session\SessionUtilities
     */
    protected $sessionUtilities;

    /**
     *
     * @param \Chamilo\Core\User\Storage\Repository\UserRepository $userRepository
     * @param \Chamilo\Core\User\Service\PasswordSecurity $passwordSecurity
     * @param \Chamilo\Libraries\Platform\Session\SessionUtilities $sessionUtilities
     */
    public function __construct(
        UserRepository $userRepository, PasswordSecurity $passwordSecurity, SessionUtilities $sessionUtilities
    )
    {
        $this->userRepository = $userRepository;
        $this->passwordSecurity = $passwordSecurity;
        $this->sessionUtilities = $sessionUtilities;
    }

    /**
     *
     * @param integer $identifier
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByIdentifier($identifier)
    {
        return $this->userRepository->findUserById($identifier);
    }

    /**
     * @param int $identifier
     *
     * @return null|string
     */
    public function getUserFullNameById($identifier)
    {
        $user = $this->findUserByIdentifier($identifier);
        if (!$user instanceof User)
        {
            return null;
        }

        return $user->get_fullname();
    }

    /**
     * Retrieves a user by a given security token
     *
     * @param string $securityToken
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUserBySecurityToken($securityToken)
    {
        return $this->userRepository->findUserBySecurityToken($securityToken);
    }

    /**
     * Retrieves a user by a given official code
     *
     * @param string $officialCode
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function getUserByOfficialCode($officialCode)
    {
        return $this->userRepository->findUserByOfficialCode($officialCode);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @param integer $offset
     * @param integer $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsers($condition, $offset = 0, $count = - 1, $orderProperty = null)
    {
        return $this->userRepository->findUsers($condition, $count, $offset, $orderProperty);
    }

    /**
     * @param int[] $userIdentifiers
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function findUsersByIdentifiers($userIdentifiers)
    {
        if (empty($userIdentifiers))
        {
            return [];
        }

        return $this->userRepository->findUsersByIdentifiersOrderedByName($userIdentifiers);
    }

    /**
     * @param string $username
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByUsername($username)
    {
        return $this->userRepository->findUserByUsername($username);
    }

    /**
     * @param $usernameOrEmail
     *
     * @return User
     */
    public function getUserByUsernameOrEmail($usernameOrEmail)
    {
        return $this->userRepository->findUserByUsernameOrEmail($usernameOrEmail);
    }

    /**
     * @param string $username
     *
     * @return bool
     */
    public function isUsernameAvailable($username)
    {
        return !$this->findUserByUsername($username) instanceof User;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $username
     * @param string $officialCode
     * @param string $emailAddress
     * @param string $password
     * @param string $authSource
     * @param bool $active
     *
     * @param \DateTime|null $expirationDate
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function createUser(
        $firstName, $lastName, $username, $officialCode, $emailAddress, $password = null, $authSource = 'Platform',
        $active = true, \DateTime $expirationDate = null
    )
    {
        $requiredParameters = [
            'firstName' => $firstName, 'lastName' => $lastName, 'username' => $username,
            'officialCode' => $officialCode, 'emailAddress' => $emailAddress
        ];

        foreach ($requiredParameters as $parameterName => $parameterValue)
        {
            if (empty($parameterValue))
            {
                throw new \InvalidArgumentException('The ' . $parameterName . ' can not be empty');
            }
        }

        if (empty($password))
        {
            $password = uniqid();
        }

        if (!$this->isUsernameAvailable($username))
        {
            throw new \RuntimeException('The given username is already taken');
        }

        $user = new User();

        $user->set_firstname($firstName);
        $user->set_lastname($lastName);
        $user->set_username($username);
        $user->set_official_code($officialCode);
        $user->set_email($emailAddress);
        $user->set_auth_source($authSource);

        if($active)
        {
            $user->set_activation_date(time());
        }

        $user->set_active($active);

        if($expirationDate instanceof \DateTime)
        {
            $user->set_expiration_date($expirationDate->getTimestamp());
        }

        $this->passwordSecurity->setPasswordForUser($user, $password);

        if (!$this->userRepository->create($user))
        {
            throw new \RuntimeException('Could not create the user');
        }

        return $user;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $username
     * @param null $officialCode
     * @param string|null $emailAddress
     * @param string|null $password
     * @param string|null $authSource
     * @param bool|null $active
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function updateUserByValues(
        User $user, string $firstName = null, string $lastName = null, string $username = null, $officialCode = null,
        string $emailAddress = null, string $password = null, string $authSource = null,
        bool $active = null
    )
    {
        if (!empty($firstName))
        {
            $user->set_firstname($firstName);
        }

        if (!empty($lastName))
        {
            $user->set_lastname($lastName);
        }

        if (!empty($username))
        {
            $user->set_username($username);
        }

        if (!empty($officialCode))
        {
            $user->set_official_code($officialCode);
        }

        if (!empty($emailAddress))
        {
            $user->set_email($emailAddress);
        }

        if (!empty($authSource))
        {
            $user->set_auth_source($authSource);
        }

        if (!is_null($active))
        {
            if($active && $user->get_active() == false)
            {
                $user->set_activation_date(time());
            }

            $user->set_active($active);
        }

        if (!empty($password))
        {
            $this->passwordSecurity->setPasswordForUser($user, $password);
        }

        if (!$this->userRepository->update($user))
        {
            throw new \RuntimeException('Could not update the user');
        }

        return $user;
    }

    /**
     * @return string
     */
    public function generateUniqueUsername()
    {
        do
        {
            $username = $this->generateUsername();
            $user = $this->getUserByUsernameOrEmail($username);
        }
        while ($user instanceof User);

        return $username;
    }

    /**
     * @return string
     */
    protected function generateUsername()
    {
        $username = '';

        for ($i = 0; $i < 3; $i ++)
        {
            $username .= chr(rand(97, 122));
        }

        $username .= rand(100, 999);

        return $username;
    }

    /**
     * Validates whether or not the currently logged in user (determined by the session) is the same as the given
     * user. This function is used to check if any manipulations to the user object were made.
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return bool
     */
    public function isUserCurrentLoggedInUser(User $user)
    {
        return $this->sessionUtilities->get('_uid') == $user->getId();
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     *
     * @return integer
     */
    public function countUsers($condition)
    {
        return $this->userRepository->countUsers($condition);
    }

}

