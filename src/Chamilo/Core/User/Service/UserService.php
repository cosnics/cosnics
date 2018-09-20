<?php

namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\Hashing\HashingUtilities;

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
    private $userRepository;

    /**
     * @var HashingUtilities
     */
    protected $hashingUtilities;

    /**
     *
     * @param \Chamilo\Core\User\Storage\Repository\UserRepository $userRepository
     * @param \Chamilo\Libraries\Hashing\HashingUtilities $hashingUtilities
     */
    public function __construct(UserRepository $userRepository, HashingUtilities $hashingUtilities)
    {
        $this->userRepository = $userRepository;
        $this->hashingUtilities = $hashingUtilities;
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
        if(!$user instanceof User)
        {
            return null;
        }

        return $user->get_fullname();
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
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function createUser(
        $firstName, $lastName, $username, $officialCode, $emailAddress, $password, $authSource = 'Platform'
    )
    {
        $requiredParameters = [
            'firstName' => $firstName, 'lastName' => $lastName, 'username' => $username,
            'officialCode' => $officialCode, 'emailAddress' => $emailAddress, 'password' => $password
        ];

        foreach($requiredParameters as $parameterName => $parameterValue)
        {
            if (empty($parameterValue))
            {
                throw new \InvalidArgumentException('The ' . $parameterName . ' can not be empty');
            }
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

        $user->set_password($this->hashingUtilities->hashString($password));

        if (!$this->userRepository->create($user))
        {
            throw new \RuntimeException('Could not create the user');
        }

        return $user;
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

