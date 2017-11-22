<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;

/**
 *
 * @package Chamilo\Libraries\Authentication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class CredentialsAuthentication extends Authentication
{
    const PARAM_LOGIN = 'login';
    const PARAM_PASSWORD = 'password';

    /**
     *
     * @var string
     */
    private $userName;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $user;

    /**
     *
     * @param string $userName
     */
    public function __construct($userName = null)
    {
        $this->userName = $userName;

        if ($userName)
        {
            $this->user = \Chamilo\Core\User\Storage\DataManager::retrieveUserByUsername($userName);
        }
    }

    /**
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     *
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Checks if the given username and password are valid
     *
     * @param string $password
     * @return boolean
     * @throws \Chamilo\Libraries\Authentication\AuthenticationException
     */
    abstract public function login($password);

    /**
     *
     * @param string $authenticationMethod
     * @param string $userName
     * @return \Chamilo\Libraries\Authentication\CredentialsAuthentication
     */
    public static function factory($authenticationMethod, $userName)
    {
        $authenticationClass = __NAMESPACE__ . '\\' . $authenticationMethod . '\\' . $authenticationMethod .
             'Authentication';
        return new $authenticationClass($userName);
    }

    /**
     *
     * @see \Chamilo\Libraries\Authentication\Authentication::logout()
     */
    public function logout($user)
    {
        parent::logout($user);

        $redirect = new Redirect(array(), array(Application::PARAM_ACTION, Application::PARAM_CONTEXT));
        $redirect->toUrl();
        exit();
    }
}
