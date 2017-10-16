<?php

namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client\Client;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestClient;
use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestResult;

/**
 * @package Chamilo\Libraries\Protocol\Webservice\Rest\Client\Client
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Curl extends RestClient
{

    /**
     * @var resource
     */
    private $curl;

    /**
     *
     * @var string
     */
    protected $cookieFile;

    /**
     * Curl constructor.
     *
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        parent::__construct($baseUrl);
        $this->set_mode(self::MODE_CURL);

        $this->set_check_target_certificate(false);
    }

    /**
     * @param bool $responseMimeType
     *
     * @return RestResult
     */
    public function request($responseMimeType = false)
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
            $this->add_header('Content-type', $this->get_data_mimeType());
        }

        if (count($this->get_headers()) > 0)
        {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->get_headers());
        }

        // cookies
        $temporaryPath = Path::getInstance()->getTemporaryPath();
        if (!is_dir($temporaryPath))
        {
            Filesystem::create_dir($temporaryPath);
        }

        $this->cookieFile = $temporaryPath . 'curl_cookies.txt';

        if (!file_exists($this->cookieFile))
        {
            fopen($this->cookieFile, 'w') or die('cannot create goofie file');
        }

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->cookieFile);

        $responseContent = curl_exec($this->curl);
        $responseHttpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $responseContentType = curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE);

        if (!$responseMimeType)
        {
            $responseMimeType = self::extract_mime_type($responseContentType);
        }

        $responseError = curl_error($this->curl);

        $result = RestResult::content_type_factory($responseMimeType);
        $result->set_request_url($url);

        $result->set_request_method($this->get_method());
        $result->set_request_data($this->get_data());

        $result->set_response_content($responseContent);
        $result->set_response_code($responseHttpCode);

        $result->set_response_cookies($this->parse_cookie_file());

        if (isset($responseError) && strlen($responseError) > 0)
        {
            $result->set_response_error($responseError);
        }
        elseif ($responseHttpCode < 200 || $responseHttpCode >= 300)
        {
            $result->set_response_error(
                'The REST request returned an HTTP error code of ' . $responseHttpCode . ' (' .
                $this->get_http_code_translation($responseHttpCode) . ')'
            );
        }

        curl_close($this->curl);

        return $result;
    }

    /**
     * @return string[]
     *
     * @deprecated
     *
     * @see parseCookieFile
     */
    public function parse_cookie_file()
    {
        return $this->parseCookieFile();
    }

    /**
     * @return string[]
     */
    public function parseCookieFile()
    {
        $aCookies = array();
        $aLines = file($this->cookieFile);
        foreach ($aLines as $line)
        {
            if ('#' == $line{0})
            {
                continue;
            }
            $arr = explode("\t", $line);
            if (isset($arr[5]) && isset($arr[6]))
            {
                $aCookies[$arr[5]] = $arr[6];
            }
        }

        return $aCookies;
    }

    /**
     *
     * @return resource
     *
     * @deprecated
     *
     * @see getCurl
     */
    public function get_curl()
    {
        return $this->getCurl();
    }

    /**
     *
     * @param resource $curl
     *
     * @deprecated
     *
     * @see setCurl
     */
    public function set_curl($curl)
    {
        $this->setCurl($curl);
    }

    /**
     *
     * @return resource
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     *
     * @param resource $curl
     */
    public function setCurl($curl)
    {
        $this->curl = $curl;
    }
}
