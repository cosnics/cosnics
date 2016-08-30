<?php
namespace Chamilo\Libraries\Authentication\SecurityToken;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Authentication\AuthenticationException;
use Chamilo\Libraries\Authentication\QueryAuthentication;
use Chamilo\Libraries\Platform\Translation;

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
     *
     * @param string $username
     * @param string $password
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws AuthenticationException
     */
    public function login()
    {
        $securityToken = $this->getRequest()->query->get(User :: PROPERTY_SECURITY_TOKEN);

        if ($securityToken)
        {
            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_user_by_security_token($securityToken);

            if (! $user instanceof User)
            {
                throw new AuthenticationException(Translation :: get('InvalidSecurityToken'));
            }

            return $user;
        }
        else
        {
            return null;
        }
    }
}
