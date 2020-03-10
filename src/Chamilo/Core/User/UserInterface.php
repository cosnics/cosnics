<?php
namespace Chamilo\Core\User;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * An interface that indicates that a class implements (special) usage of users and forces the implementing context to
 * provide a given number of methods to safeguard compatibility with the user application
 * 
 * @author Hans De Bisschop
 */
interface UserInterface
{

    /**
     *
     * @param \user\User $user
     * @return string
     */
    public static function get_additional_user_information(User $user);
}