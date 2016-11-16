<?php
namespace Chamilo\Libraries\Architecture\Interfaces;

/**
 * An authentication class implements the <code>UserRegistrationSupport</code> interface to indicate that it supports
 * registration of new users
 * 
 * @package Chamilo\Libraries\Architecture\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface UserRegistrationSupport
{

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     */
    public function registerUser();
}
