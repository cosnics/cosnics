<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket\DataConnector;

/**
 * Description of mediamosa_rest_clientclass
 * 
 * @author jevdheyd
 */
class RestClient extends \Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestClient
{

    private $bitbucket_url;
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const RESPONSE_TYPE_STRING = '0';
    const RESPONSE_TYPE_XML = '1';
    const RESPONSE_TYPE_JSON = '2';

    public function __construct($bitbucket_url)
    {
        parent::__construct();
        
        $this->bitbucket_url = $bitbucket_url;
    }

    public function array_to_url($data)
    {
        if (is_array($data))
        {
            $tmp = array();
            
            foreach ($data as $key => $value)
            {
                if (is_array($value))
                {
                    $subtmp = array();
                    
                    foreach ($value as $subkey => $subvalue)
                    {
                        $tmp[] = $key . '[]' . '=' . $subvalue;
                    }
                }
                else
                {
                    $tmp[] = $key . '=' . $value;
                }
            }
            return implode('&', $tmp);
        }
    }

    /*
     * a prefab function for a request @param method string @param url string @param data array @return
     * MediaMosaRestResult object
     */
    public function request($method, $url, $data = null, $response_type = self::RESPONSE_TYPE_XML)
    {
        $this->set_http_method($method);
        
        $this->set_data_to_send('');
        
        // different method need different handling of data
        if (($method == self::METHOD_POST) || ($method == self::METHOD_PUT) || ($method == self::METHOD_DELETE))
        {
            // if (is_array($data))
            $this->set_data_to_send($data);
            $url = $this->bitbucket_url . $url;
        }
        elseif ($method == self::METHOD_GET)
        {
            if (is_array($data))
            {
                $tmp = array();
                
                foreach ($data as $key => $value)
                {
                    if (is_array($value))
                    {
                        $subtmp = array();
                        
                        foreach ($value as $subkey => $subvalue)
                        {
                            $tmp[] = $key . '[]' . '=' . $subvalue;
                        }
                    }
                    else
                    {
                        $tmp[] = $key . '=' . $value;
                    }
                }
                
                $get_string = implode('&', $tmp);
                $url .= '?' . $get_string;
            }
            $url = $this->bitbucket_url . $url;
        }
        
        $this->set_url($url);
        
        $response = $this->send_request();
        if ($response_type == self::RESPONSE_TYPE_XML)
        {
            $response->set_response_content_xml();
        }
        
        return $response;
    }
}
