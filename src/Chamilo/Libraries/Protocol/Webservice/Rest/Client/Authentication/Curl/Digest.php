<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client\Authentication\Curl;

class Digest extends Basic
{

    public function authenticate()
    {
        parent :: authenticate();
        curl_setopt($this->get_client()->get_curl(), CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($this->get_client()->get_curl(), CURLOPT_HTTPHEADER, array("X-Requested-Auth: Digest"));
    }
}
