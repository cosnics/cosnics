<?php
namespace Chamilo\Libraries\Authentication\SecurityToken;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\QueryAuthentication;

/**
 *
 * @package Chamilo\Libraries\Authentication\SecurityToken$SecurityTokenAuthentication
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SecurityTokenAuthentication extends QueryAuthentication
{
    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User
     *
     * @throws AuthenticationException
     */
    public function login()
    {
        $securityToken = $this->getRequest()->query->get(User :: PROPERTY_SECURITY_TOKEN);

        if ($securityToken)
        {
            return $this->retrieveUserBySecurityToken($securityToken);

        }

        return null;
    }

}
