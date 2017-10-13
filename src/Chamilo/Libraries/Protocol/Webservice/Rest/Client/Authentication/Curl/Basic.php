<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client\Authentication\Curl;

use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestAuthentication;

/**
 * @package Chamilo\Libraries\Protocol\Webservice\Rest\Client\Authentication\Curl
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Basic extends RestAuthentication
{

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     *
     * @return string
     */
    public function get_login()
    {
        return $this->login;
    }

    /**
     *
     * @return string
     */
    public function get_password()
    {
        return $this->password;
    }

    /**
     *
     * @param string $login
     */
    public function set_login($login)
    {
        $this->login = $login;
    }

    /**
     *
     * @param string $password
     */
    public function set_password($password)
    {
        $this->password = $password;
    }

    public function authenticate()
    {
        curl_setopt($this->get_client()->get_curl(), CURLOPT_USERPWD, $this->login . ':' . $this->password);
    }
}
