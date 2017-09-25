<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Core\User\Storage\Repository\UserRepository;

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
     *
     * @param \Chamilo\Core\User\Storage\Repository\UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\Repository\UserRepository
     */
    protected function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     *
     * @param \Chamilo\Core\User\Storage\Repository\UserRepository $userRepository
     */
    protected function setUserRepository(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     *
     * @param integer $identifier
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserByIdentifier($identifier)
    {
        return $this->getUserRepository()->findUserById($identifier);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @param integer $count
     * @param integer $offset
     * @param \Chamilo\Libraries\Storage\Query\OrderBy[] $orderProperty
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findUsers($condition, $offset = 0, $count = -1, $orderProperty = null)
    {
        return $this->getUserRepository()->findUsers($condition, $count, $offset, $orderProperty);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition|null $condition
     * @return integer
     */
    public function countUsers($condition)
    {
        return $this->getUserRepository()->countUsers($condition);
    }
}

