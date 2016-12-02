<?php
/**
 * API Wrapper for SoundCloud written in PHP with support for authication using OAuth.
 * 
 * @package Soundcloud
 * @version 1.2.0
 * @author Anton Lindqvist <anton@qvister.se>
 * @link http://github.com/mptre/php-soundcloud/
 */
class Soundcloud
{
    const VERSION = '1.2.0';

    public static $api_version = 1;

    public static $domains = array('development' => 'sandbox-soundcloud.com', 'production' => 'soundcloud.com');

    public static $oauth_paths = array(
        'access' => '/oauth/access_token', 
        'authorize' => '/oauth/authorize', 
        'request' => '/oauth/request_token');

    public $development;

    public function __construct($consumer_key, $consumer_secret, $oauth_token = null, $oauth_token_secret = null, 
        $development = false)
    {
        if (empty($consumer_key))
        {
            throw new SoundcloudException('Consumer Key required for all requests, even those to public resources.');
        }
        
        $this->development = $development;
        
        $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
        $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);
        
        if (! empty($oauth_token) && ! empty($oauth_token_secret))
        {
            $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret);
        }
        else
        {
            $this->token = null;
        }
    }

    public function get_authorize_url($token)
    {
        if (is_array($token))
        {
            $token = $token['oauth_token'];
        }
        
        return $this->_get_url('authorize') . '?oauth_token=' . $token;
    }

    public function get_request_token($oauth_callback)
    {
        $request = $this->request($this->_get_url('request'), 'POST', array('oauth_callback' => $oauth_callback));
        $token = $this->_parse_response($request);
        
        $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
        
        return $token;
    }

    public function get_access_token($token)
    {
        $response = $this->request($this->_get_url('access'), 'POST', array('oauth_verifier' => $token));
        $token = $this->_parse_response($response);
        $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
        
        return $token;
    }

    public function upload_track($post_data, $asset_data_mime = null, $artwork_data_mime = null)
    {
        $body = '';
        $boundary = '---------------------------' . md5(rand());
        $crlf = "\r\n";
        $headers = array('Content-Type' => 'multipart/form-data; boundary=' . $boundary, 'Content-Length' => 0);
        
        foreach ($post_data as $key => $val)
        {
            if (in_array($key, array('track[asset_data]', 'track[artwork_data]')))
            {
                $body .= '--' . $boundary . $crlf;
                $body .= 'Content-Disposition: form-data; name="' . $key . '"; filename="' . basename($val) . '"' . $crlf;
                $body .= 'Content-Type: ' . (($key == 'track[asset_data]') ? $asset_data_mime : $artwork_data_mime) .
                     $crlf;
                $body .= $crlf;
                $body .= file_get_contents($val) . $crlf;
            }
            else
            {
                $body .= '--' . $boundary . $crlf;
                $body .= 'Content-Disposition: form-data; name="' . $key . '"' . $crlf;
                $body .= $crlf;
                $body .= $val . $crlf;
            }
        }
        
        $body .= '--' . $boundary . '--' . $crlf;
        $headers['Content-Length'] += strlen($body);
        
        return $this->request('tracks', 'POST', $body, $headers);
    }

    public function request($resource, $method = 'GET', $args = array(), $headers = null)
    {
        if (! preg_match('/http:\/\//', $resource))
        {
            $url = $this->_get_url() . $resource;
        }
        else
        {
            $url = $resource;
        }
        
        if (stristr($headers['Content-Type'], 'multipart/form-data'))
        {
            $body = false;
        }
        elseif (stristr($headers['Content-Type'], 'application/xml'))
        {
            $body = false;
        }
        else
        {
            $body = true;
        }
        
        $request = OAuthRequest :: from_consumer_and_token(
            $this->consumer, 
            $this->token, 
            $method, 
            $url, 
            ($body === true) ? $args : null);
        $request->sign_request($this->sha1_method, $this->consumer, $this->token);
        
        // Formerly $url was $request->get_normalized_http_url(), which prevented params from being passed.
        return $this->_curl($url, $request, $args, $headers);
    }

    private function _build_header($headers)
    {
        $h = array();
        
        if (count($headers) > 0)
        {
            foreach ($headers as $key => $val)
            {
                $h[] = $key . ': ' . $val;
            }
            
            return $h;
        }
        else
        {
            return $headers;
        }
    }

    private function _curl($url, $request, $post_data = null, $headers = null)
    {
        $ch = curl_init();
        $mime = (stristr($headers['Content-Type'], 'multipart/form-data')) ? true : false;
        $headers['User-Agent'] = (isset($headers['User-Agent'])) ? $headers['User-Agent'] : 'PHP SoundCloud/' .
             self :: VERSION;
        $headers['Content-Length'] = (isset($headers['Content-Length'])) ? $headers['Content-Length'] : 0;
        $headers = (is_array($headers)) ? $this->_build_header($headers) : array();
        $options = array(CURLOPT_URL => $url, CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => true);
        
        if (in_array($request->get_normalized_http_method(), array('DELETE', 'PUT')))
        {
            $options[CURLOPT_CUSTOMREQUEST] = $request->get_normalized_http_method();
            $options[CURLOPT_POSTFIELDS] = '';
        }
        
        if (is_array($post_data) && count($post_data) > 0 || $mime === true)
        {
            $options[CURLOPT_POSTFIELDS] = (is_array($post_data) && count($post_data) == 1) ? ((isset(
                $post_data[key($post_data)])) ? $post_data[key($post_data)] : $post_data) : $post_data;
            $options[CURLOPT_POST] = true;
        }
        
        $headers[] = $request->to_header();
        $options[CURLOPT_HTTPHEADER] = $headers;
        
        curl_setopt_array($ch, $options);
        
        $response = curl_exec($ch);
        $this->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        return $response;
    }

    private function _get_url($type = null)
    {
        if ($type == 'authorize')
        {
            return 'http://' . (($this->development) ? self :: $domains['development'] : self :: $domains['production']) .
                 self :: $oauth_paths[$type];
        }
        
        $url = 'http://api.';
        $url .= ($this->development) ? self :: $domains['development'] : self :: $domains['production'];
        $url .= '/v' . self :: $api_version;
        $url .= (array_key_exists($type, self :: $oauth_paths)) ? self :: $oauth_paths[$type] : null;
        $url .= '/';
        
        return $url;
    }

    private function _parse_response($response)
    {
        parse_str($response, $output);
        
        return (count($output) > 0) ? $output : false;
    }
}
class SoundcloudException extends Exception
{
    // kthxbye!
}
