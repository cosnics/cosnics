<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Libraries\Authentication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class QueryAuthentication extends Authentication
{

    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     *
     * @param string $userName
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws AuthenticationException
     */
    abstract public function login();

    /**
     *
     * @param string $authenticationMethod
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Chamilo\Libraries\Authentication\CredentialsAuthentication
     */
    public static function factory($authenticationMethod, \Symfony\Component\HttpFoundation\Request $request)
    {
        $authenticationClass = __NAMESPACE__ . '\\' . $authenticationMethod . '\\' . $authenticationMethod .
             'Authentication';
        return new $authenticationClass($request);
    }

    /**
     * Retrieves a user by a given security token
     * 
     * @param $securityToken
     * @return User
     *
     * @throws AuthenticationException
     */
    protected function retrieveUserBySecurityToken($securityToken)
    {
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_user_by_security_token($securityToken);
        
        if (! $user instanceof User)
        {
            throw new AuthenticationException(Translation::get('InvalidSecurityToken'));
        }
        
        return $user;
    }
}
