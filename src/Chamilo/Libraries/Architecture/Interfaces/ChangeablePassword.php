<?php
namespace Chamilo\Libraries\Architecture\Interfaces;

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
     * @param string $oldPassword
     * @param string $newPassword
     *
     * @return boolean
     */
    public function changePassword($oldPassword, $newPassword);

    /**
     * Get the password requirements for the authentication method
     *
     * @return string
     */
    public function getPasswordRequirements();
}
