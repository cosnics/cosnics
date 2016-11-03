<?php
namespace Chamilo\Libraries\Authentication;

/**
 *
 * @package Chamilo\Libraries\Authentication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ExternalAuthentication extends Authentication
{

    /**
     *
     * @throws AuthenticationException
     */
    abstract public function login();
}
