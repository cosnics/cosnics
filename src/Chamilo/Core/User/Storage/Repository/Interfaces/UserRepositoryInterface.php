<?php
namespace Chamilo\Core\User\Storage\Repository\Interfaces;

use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * Interface for the user repository
 *
 * @package user
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface UserRepositoryInterface
{

    /**
     * Finds a user by a given id
     *
     * @param int $id
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function findUserById($id);

    /**
     * Finds a user by a list of parameters
     *
     * @param Condition $condition
     * @param int $count
     * @param int $offset
     * @param OrderBy[] $order_by
     *
     * @return User[]
     */
    public function findUsers(Condition $condition, $count = null, $offset = null, $order_by = array());

    /**
     * Finds a user by a given email
     *
     * @param string $email
     *
     * @return User;
     */
    public function findUserByEmail($email);

    /**
     *
     * @return User[]
     */
    public function findActiveStudents();

    /**
     *
     * @return User[]
     */
    public function findActiveTeachers();
}