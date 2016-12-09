<?php
namespace Chamilo\Libraries\Protocol\Webservice\Rest\Client\Type;

use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestClient;
use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestResult;
use Chamilo\Libraries\Utilities\StringUtilities;

class PearRestClient extends RestClient
{

    private $pear;

    public function __construct($base_url)
    {
        parent :: __construct($base_url);
        $this->set_mode(self :: MODE_PEAR);

        $this->set_check_target_certificate(false);
    }

    public function request($response_mime_type = false)
    {
        $result = new RestResult();
        $result->set_request_connexion_mode($this->get_mode());
        $result->set_request_http_method($this->get_method());
        $result->set_request_sent_data($this->get_data());
        $result->set_request_url($this->get_url());

        $request_properties = array();
        $request_properties['method'] = $this->get_http_method();
        $request_properties['user'] = $this->get_basic_login();
        $request_properties['pass'] = $this->get_basic_password();

        $request = new \HTTP_Request($this->get_url(), $request_properties);

        /*
         * addition
         */
        // possibly set a proxy
        if ($proxy = $this->get_proxy())
            $request->setProxy($proxy['server'], $proxy['port']);

            // add data
        if ($this->has_data())
        {
            $data_to_send = $this->get_data();
            if (is_string($data_to_send))
            {
                $request->setBody($data_to_send);
            }
            elseif (is_array($data_to_send) && isset($data_to_send['content']))
            {
                /*
                 * If $this->data_to_send is an array and the content to send is in $this->data_to_send['content'], we
                 * use it
                 */
                // $request->addPostData('content', $this->data_to_send['content'], true);
                $request->setBody($data_to_send['content']);
            }
            elseif (is_array($data_to_send) && isset($data_to_send['file']))
            {
                if (is_array($data_to_send['file']))
                {
                    $values = array_values($data_to_send['file']);
                    if (count($values) > 0)
                    {
                        $file_path = $values[0];

                        if ((string) StringUtilities :: getInstance()->createString($file_path)->startsWith('@'))
                        {
                            $file_path = substr($file_path, 1);
                        }

                        if (file_exists($file_path))
                        {
                            /*
                             * The file is on the HD, and therefore must be read to be set in the body
                             */
                            $file_content = file_get_contents($file_path);
                        }
                    }
                }
                else
                {
                    /*
                     * Tries to use the file value as the content of a file in memory
                     */
                    $file_content = $data_to_send['file'];
                }

                $request->setBody($file_content);
            }
            /*
             * addition
             */
            elseif (is_array($data_to_send))
            {
                foreach ($data_to_send as $key => $value)
                {
                    $request->addPostData($key, $value);
                }
            }

            /*
             * If the mime type is given as a parameter, we use it to set the content-type request
             */
            if (is_array($data_to_send) && isset($data_to_send['mime']))
            {
                $request->addHeader('Content-type', $data_to_send['mime']);
            }

            /*
             * addition
             */
            /*add additional headers*/

            if (is_array($this->get_header_data()))
            {
                foreach ($this->get_header_data() as $n => $header)
                {
                    $request->addHeader($header['name'], $header['value']);
                }
            }
        }

        $req_result = $request->sendRequest(true);
        if ($req_result === true)
        {
            $result->set_response_http_code($request->getResponseCode());
            $result->set_response_content($request->getResponseBody());
            /*
             * addition
             */
            $result->set_response_header($request->getResponseHeader());
            $result->set_response_cookies($request->getResponseCookies());
        }
        else
        {
            $result->set_response_http_code($request->getResponseCode());
            $result->set_response_error($request->getResponseReason());
        }

        return $result;
    }

    /**
     *
     * @return the $pear
     */
    public function get_pear()
    {
        return $this->pear;
    }

    /**
     *
     * @param $pear the $curl to set
     */
    public function set_pear($pear)
    {
        $this->pear = $pear;
    }
}
