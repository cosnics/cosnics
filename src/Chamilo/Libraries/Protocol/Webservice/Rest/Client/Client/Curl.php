<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client\Client;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestClient;
use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestResult;

class Curl extends RestClient
{

    private $curl;

    /**
     * @var string
     */
    protected $cookie_file;

    public function __construct($base_url)
    {
        parent :: __construct($base_url);
        $this->set_mode(self :: MODE_CURL);

        $this->set_check_target_certificate(false);
    }

    public function request($response_mime_type = false)
    {
        $url = $this->get_resource_url();
        $this->curl = curl_init($url);

        curl_setopt($this->curl, CURLOPT_USERAGENT, 'Chamilo2Bot/1.0');
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $this->get_method());
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);

        if ($this->get_check_target_certificate())
        {
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, true);
        }
        else
        {
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        }

        if ($this->has_authentication())
        {
            $this->get_authentication()->authenticate();
        }

        if ($this->has_data())
        {
            curl_setopt($this->curl, CURLOPT_POST, 1);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->get_data()->prepare());
        }

        if ($this->has_data_mimetype())
        {
            $this->add_header('Content-type', $this->get_data_mimetype());
        }

        if (count($this->get_headers()) > 0)
        {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->get_headers());
        }

        // cookies
        $temporaryPath = Path :: getInstance()->getTemporaryPath();
        if(!is_dir($temporaryPath))
        {
            Filesystem::create_dir($temporaryPath);
        }

        $this->cookie_file = $temporaryPath . 'curl_cookies.txt';

        if (! file_exists($this->cookie_file))
        {
            fopen($this->cookie_file, 'w') or die('cannot create goofie file');
        }

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->cookie_file);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->cookie_file);

        $response_content = curl_exec($this->curl);
        $response_http_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $response_content_type = curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE);

        if (! $response_mime_type)
        {
            $response_mime_type = self :: extract_mime_type($response_content_type);
        }

        $response_error = curl_error($this->curl);

        $result = RestResult :: content_type_factory($response_mime_type);
        $result->set_request_url($url);

        $result->set_request_method($this->get_method());
        $result->set_request_data($this->get_data());

        $result->set_response_content($response_content);
        $result->set_response_code($response_http_code);

        $result->set_response_cookies($this->parse_cookie_file());

        if (isset($response_error) && strlen($response_error) > 0)
        {
            $result->set_response_error($response_error);
        }
        elseif ($response_http_code < 200 || $response_http_code >= 300)
        {
            $result->set_response_error(
                'The REST request returned an HTTP error code of ' . $response_http_code . ' (' .
                     $this->get_http_code_translation($response_http_code) . ')');
        }

        curl_close($this->curl);

        return $result;
    }

    public function parse_cookie_file()
    {
        $aCookies = array();
        $aLines = file($this->cookie_file);
        foreach ($aLines as $line)
        {
            if ('#' == $line{0})
                continue;
            $arr = explode("\t", $line);
            if (isset($arr[5]) && isset($arr[6]))
                $aCookies[$arr[5]] = $arr[6];
        }

        return $aCookies;
    }

    /**
     *
     * @return the $curl
     */
    public function get_curl()
    {
        return $this->curl;
    }

    /**
     *
     * @param $curl the $curl to set
     */
    public function set_curl($curl)
    {
        $this->curl = $curl;
    }
}
