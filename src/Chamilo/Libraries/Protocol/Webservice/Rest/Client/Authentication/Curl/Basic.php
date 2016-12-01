<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client\Authentication\Curl;

use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestAuthentication;

class Basic extends RestAuthentication
{

    private $login;

    private $password;

    /**
     *
     * @return the $login
     */
    public function get_login()
    {
        return $this->login;
    }

    /**
     *
     * @return the $password
     */
    public function get_password()
    {
        return $this->password;
    }

    /**
     *
     * @param $login the $login to set
     */
    public function set_login($login)
    {
        $this->login = $login;
    }

    /**
     *
     * @param $password the $password to set
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
