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
    
    public function changePassword(User $user, string $oldPassword, string $newPassword): bool;

    /**
     * Get the password requirements for the authentication method
     */
    public function getPasswordRequirements(): string;
}
