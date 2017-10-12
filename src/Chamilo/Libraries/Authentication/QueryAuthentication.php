<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Platform\ChamiloRequest;

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
     * @var \Chamilo\Libraries\Platform\ChamiloRequest
     */
    private $request;

    /**
     *
     * @param string $userName
     */
    public function __construct(\Chamilo\Libraries\Platform\ChamiloRequest $request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\ChamiloRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     */
    public function setRequest(\Chamilo\Libraries\Platform\ChamiloRequest $request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     */
    abstract public function login();

    /**
     *
     * @param string $authenticationMethod
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @return \Chamilo\Libraries\Authentication\QueryAuthentication
     */
    public static function factory($authenticationMethod, ChamiloRequest $request)
    {
        $authenticationClass = __NAMESPACE__ . '\\' . $authenticationMethod . '\\' . $authenticationMethod .
             'Authentication';
        return new $authenticationClass($request);
    }

    /**
     * Retrieves a user by a given security token
     *
     * @param $securityToken
     * @return \Chamilo\Core\User\Storage\DataClass\User
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
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
