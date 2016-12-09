<?php
namespace Chamilo\Libraries\Architecture\Interfaces;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * An authentication class implements the <code>ChangeablePassword</code>
 * interface to indicate that it supports changing of passwords
 * 
 * @package Chamilo\Libraries\Architecture\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface ChangeablePassword
{

    /**
     *
     * @param User $user
     * @param string $oldPassword
     * @param string $newPassword
     *
     * @return bool
     */
    public function changePassword(User $user, $oldPassword, $newPassword);

    /**
     * Get the password requirements for the authentication method
     * 
     * @return string
     */
    public function getPasswordRequirements();
}
