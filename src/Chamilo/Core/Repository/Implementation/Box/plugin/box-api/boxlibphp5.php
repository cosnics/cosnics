<?php

/**
 * Box REST Client Library for PHP5 Developers
 * 
 * @author James Levy <james@box.net>
 * @link http://enabled.box.net
 * @access public
 * @version 1.0 copyright Box.net 2007 Available for use and distribution under GPL-license Go to
 *          http://www.gnu.org/licenses/gpl-3.0.txt for full text
 */
require_once 'class.curl.php';
class boxclient
{

    public function __construct($api_key, $auth_token)
    {
        $this->api_key = $api_key;
        $this->auth_token = $auth_token;
    }
    // Toggle Debug Mode
    public $_debug = false;
    
    // Setup variables
    public $_box_api_url = 'http://www.box.net/api/1.0/rest';

    public $_box_api_upload_url = 'http://upload.box.net/api/1.0/upload';

    public $_box_api_download_url = 'https://www.box.net/api/1.0/download';

    public $_error_code = '';

    public $_error_msg = '';
    
    // Setup for Functions
    public function makeRequest($method, $params = array())
    {
        $this->_clearErrors();
        $useCURL = in_array('curl', get_loaded_extensions());
        
        if ($method == 'upload')
        {
            $args = array();
            foreach ($params as $k => $v)
            {
                array_push($args, urlencode($v));
                $query_str = implode('/', $args);
            }
            $query_str = rtrim($query_str, '/');
            $request = $this->_box_api_upload_url . '/' . $query_str;
            if ($this->_debug)
            {
                echo "Upload Request: " . $request;
            }
        }
        else 
            if ($method == 'download')
            {
                $args = array();
                foreach ($params as $k => $v)
                {
                    array_push($args, urlencode($v));
                    $query_str = implode('/', $args);
                }
                $request = $this->_box_api_download_url . '/' . $query_str;
                if ($this->_debug)
                {
                    echo "Download Request: " . $request;
                }
            }
            else
            {
                $args = array();
                foreach ($params as $k => $v)
                {
                    array_push($args, urlencode($k) . '=' . urlencode($v));
                    $query_str = implode('&', $args);
                }
                $request = $this->_box_api_url . '?' . $method . '&' . $query_str;
                if ($this->_debug)
                {
                    echo "Request: " . $request;
                }
            }
        
        if ($useCURL)
        {
            $c = new curl($request);
            $c->setopt(CURLOPT_FOLLOWLOCATION, true);
            $xml = $c->exec();
            $error = $c->hasError();
            if ($error)
            {
                $this->_error_msg = $error;
                return false;
            }
            $c->close();
        }
        else
        {
            $url_parsed = parse_url($request);
            $host = $url_parsed["host"];
            $port = ($url_parsed['port'] == 0) ? 80 : $url_parsed['port'];
            $path = '';
            $path = $url_parsed["path"] . (($url_parsed['query'] != '') ? $path .= "?{$url_parsed[query]}" : '');
            $headers = "GET $path HTTP/1.0\r\n";
            $headers .= "Host: $host\r\n\r\n";
            $fp = fsockopen($host, $port, $errno, $errstr, 30);
            if (! $fp)
            {
                $this->_error_msg = $errstr;
                $this->_error_code = $errno;
                return false;
            }
            else
            {
                fwrite($fp, $headers);
                while (! feof($fp))
                {
                    $xml .= fgets($fp, 1024);
                }
                fclose($fp);
                
                $xml_start = strpos($xml, '<?xml');
                $xml = substr($xml, $xml_start, strlen($xml));
            }
        }
        
        if ($this->_debug)
        {
            echo '<h2>XML Response</h2>';
            echo '<pre class="xml">';
            echo htmlspecialchars($xml);
            echo '</pre>';
        }
        
        $xml_parser = xml_parser_create();
        xml_parse_into_struct($xml_parser, $xml, $data);
        xml_parser_free($xml_parser);
        return $data;
    }
    
    //
    
    // ////// Functions
    
    // Get Ticket
    public function getTicket($params = array())
    {
        $params['api_key'] = $this->api_key;
        $ret_array = array();
        
        $data = $this->makeRequest('action=get_ticket', $params);
        if ($this->_checkForError($data))
        {
            return false;
        }
        foreach ($data as $a)
        {
            switch ($a['tag'])
            {
                case 'STATUS' :
                    $ret_array['status'] = $a['value'];
                    break;
                case 'TICKET' :
                    $ret_array['ticket'] = $a['value'];
                    break;
            }
        }
        
        if ($this->_debug)
        {
            echo '<h2>Ticket Return</h2>';
            $this->_a($ret_array);
            print_r($a);
            echo '<hr />';
        }
        
        return $ret_array;
    }
    
    // Get Auth Token
    public function getAuthToken($ticket)
    {
        $params['api_key'] = $this->api_key;
        $params['ticket'] = $ticket;
        
        $ret_array = array();
        
        $data = $this->makeRequest('action=get_auth_token', $params);
        
        if ($this->_checkForError($data))
        {
            return false;
        }
        
        foreach ($data as $a)
        {
            switch ($a['tag'])
            {
                case 'STATUS' :
                    $ret_array['status'] = $a['value'];
                    break;
                case 'AUTH_TOKEN' :
                    $ret_array['auth_token'] = $a['value'];
                    break;
            }
        }
        
        if ($ret_array['status'] == 'get_auth_token_ok')
        {
            $auth_token = $ret_array['auth_token'];
            global $auth_token;
        }
        else
        {
            header('location: http://www.box.net/api/1.0/auth/' . $ticket);
        }
    }
    
    // Retrieve Account Tree (http://enabled.box.net/docs/rest#get_account_tree)
    public function getAccountTree($params = array())
    {
        $params['api_key'] = $this->api_key;
        $params['auth_token'] = $this->auth_token;
        $params['folder_id'] = 0;
        $params['params[]'] = 'nozip';
        $ret_array = array();
        $data = $this->makeRequest('action=get_account_tree', $params);
        if ($this->_checkForError($data))
        {
            return false;
        }
        $tree_count = count($data);
        global $tree_count;
        
        for ($i = 0, $tree_count = count($data); $i < $tree_count; $i ++)
        {
            $a = $data[$i];
            switch ($a['tag'])
            {
                case 'FOLDER' :
                    if (is_array($a['attributes']))
                    {
                        $ret_array[$i]['folder_id'] = $a['attributes']['ID'];
                        $ret_array[$i]['folder_name'] = $a['attributes']['NAME'];
                    }
                    break;
                case 'FILE' :
                    if (is_array($a['attributes']))
                    {
                        $ret_array[$i]['file_id'] = $a['attributes']['ID'];
                        $ret_array[$i]['file_name'] = $a['attributes']['FILE_NAME'];
                        $ret_array[$i]['description'] = $a['attributes']['DESCRIPTION'];
                        $ret_array[$i]['created'] = $a['attributes']['CREATED'];
                        $ret_array[$i]['updated'] = $a['attributes']['UPDATED'];
                        $ret_array[$i]['size'] = $a['attributes']['SIZE'];
                        $tree_count ++;
                    }
                    break;
            }
        }
        if ($this->_debug)
        {
            echo '<h2>Account Tree Return</h2>';
            $this->_a($ret_array);
            "<br/>";
            print_r($a);
            echo '<hr />';
        }
        
        return $ret_array;
    }

    public function get_file_info($file_id, $params = array())
    {
        $params['api_key'] = $this->api_key;
        $params['auth_token'] = $this->auth_token;
        $params['file_id'] = $file_id;
        $params['params[]'] = 'nozip';
        
        $ret_array = array();
        $data = $this->makeRequest('action=get_file_info', $params);
        if ($this->_checkForError($data))
        {
            return false;
        }
        foreach ($data as $d)
        {
            switch ($d['tag'])
            {
                case 'FILE_ID' :
                    $ret_array['file_id'] = $d['value'];
                    break;
                case 'FILE_NAME' :
                    $ret_array['file_name'] = $d['value'];
                    break;
                case 'FOLDER_ID' :
                    $ret_array['folder_id'] = $d['value'];
                    break;
                case 'CREATED' :
                    $ret_array['created'] = $d['value'];
                    break;
                case 'UPDATED' :
                    $ret_array['updated'] = $d['value'];
                    break;
            }
        }
        return $ret_array;
    }

    public function get_files($folder_id, $params = array())
    {
        $params['api_key'] = $this->api_key;
        $params['auth_token'] = $this->auth_token;
        $params['folder_id'] = 0;
        $params['params[]'] = 'nozip';
        $ret_array = array();
        $data = $this->makeRequest('action=get_account_tree', $params);
        if ($this->_checkForError($data))
        {
            return false;
        }
        $tree_count = count($data);
        $files = array();
        global $tree_count;
        
        for ($i = 0, $tree_count = count($data); $i < $tree_count; $i ++)
        {
            $a = $data[$i];
            switch ($a['tag'])
            {
                case 'FILE' :
                    if (is_array($a['attributes']))
                    {
                        $file = $this->get_file_info($a['attributes']['ID']);
                        if ($file['folder_id'] == $folder_id)
                        {
                            $files[] = $file;
                        }
                    }
                    break;
            }
        }
        return $files;
    }

    public function delete_file($file_id, $params = array())
    {
        $params['api_key'] = $this->api_key;
        $params['auth_token'] = $this->auth_token;
        $params['target'] = 'file';
        $params['target_id'] = $file_id;
        $ret_array = array();
        $data = $this->makeRequest('action=delete', $params);
        if ($this->_checkForError($data))
        {
            return false;
        }
        return true;
    }

    public function download_file($file_id, $params = array())
    {
        $params['auth_token'] = $this->auth_token;
        $params['file_id'] = $file_id;
        $handle = fopen($this->_box_api_download_url . '/' . $this->auth_token . '/' . $file_id, 'rb');
        $contents = stream_get_contents($handle);
        fclose($handle);
        return $contents;
    }

    public function rename_file($file_id, $new_name, $params = array())
    {
        $params['api_key'] = $this->api_key;
        $params['auth_token'] = $this->auth_token;
        $params['target'] = 'file';
        $params['target_id'] = $file_id;
        $params['new_name'] = $new_name;
        
        $ret_array = array();
        $data = $this->makeRequest('', $params);
        if ($this->_checkForError($data))
        {
            return false;
        }
        return true;
    }
    
    // Create New Folder
    public function createFolder($new_folder_name, $parent_id, $params = array())
    {
        $params['api_key'] = $this->api_key;
        $params['auth_token'] = $this->auth_token;
        $params['parent_id'] = $parent_id;
        $params['name'] = $new_folder_name;
        $params['share'] = 1; // Set to '1' by default. Set to '0' to make folder private.
        
        $ret_array = array();
        $data = $this->makeRequest('action=create_folder', $params);
        if ($this->_checkForError($data))
        {
            return false;
        }
        foreach ($data as $a)
        {
            switch ($a['tag'])
            {
                case 'FOLDER_ID' :
                    $ret_array['folder_id'] = $a['value'];
                    break;
                case 'FOLDER_NAME' :
                    $ret_array['folder_type'] = $a['value'];
                    break;
                case 'SHARED' :
                    $ret_array['shared'] = $a['value'];
                    break;
                case 'PASSWORD' :
                    $ret_array['password'] = $a['value'];
                    break;
            }
        }
        if ($this->_debug)
        {
            echo '<h2>Account Tree Return</h2>';
            $this->_a($ret_array);
            "<br/>";
            print_r($a);
            echo '<hr />';
        }
        return $ret_array;
    }

    public function ExportFile($file, $params = array())
    {
        $curl = curl_init($this->_box_api_upload_url . '/' . $this->auth_token . '/0');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array('file' => ('@' . $file)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($curl);
        curl_close($curl);
        if (strpos($response, 'upload_ok'))
        {
            return true;
        }
        else
            return false;
    }

    public function UploadFile($file, $params = array())
    {
        
        // $filename_header = "Content-Disposition: form-data; name=\"Filename\"\r\n\r\n" . $file['name']
        // ."\r\nBoundary:";
        // $filename_header1 = "Content-Disposition: form-data name=\"Filedata\";filename=\"".
        // rawurlencode($file['name']) ."\"\r\n\r\n";
        $curl = curl_init($this->_box_api_upload_url . '/' . $this->auth_token . '/0');
        // curl_setopt($curl, CURLOPT_HTTPHEADER, array($filename_header, $filename_header1));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array('file' => ('@' . $file['tmp_name'])));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        if (strpos($response, 'upload_ok'))
        {
            return true;
        }
        else
            return false;
    }
    
    // Register New User
    public function RegisterUser($params = array())
    {
        $params['api_key'] = $this->api_key;
        $params['login'] = $_REQUEST['login'];
        $params['password'] = $_REQUEST['password'];
        
        $ret_array = array();
        
        $data = $this->makeRequest('action=register_new_user', $params);
        
        if ($this->_checkForError($data))
        {
            return false;
        }
        
        foreach ($data as $a)
        {
            
            switch ($a['tag'])
            {
                
                case 'STATUS' :
                    
                    $ret_array['status'] = $a['value'];
                    
                    break;
                
                case 'AUTH_TOKEN' :
                    
                    $ret_array['auth_token'] = $a['value'];
                    
                    break;
                
                case 'LOGIN' :
                    
                    $ret_array['login'] = $a['value'];
                    
                    break;
                
                case 'SPACE_AMOUNT' :
                    
                    $ret_array['space_amount'] = $a['value'];
                    
                    break;
                
                case 'SPACE_USED' :
                    
                    $ret_array['space_used'] = $a['value'];
                    
                    break;
            }
        }
        
        if ($this->_debug)
        {
            echo '<h2>Registration Return</h2>';
            $this->_a($ret_array);
            print_r($a);
            
            echo '<hr />';
        }
        
        return $ret_array;
    }
    
    // Add Tags (http://enabled.box.net/docs/rest#add_to_tag)
    public function AddTag($tag, $id, $target_type, $params = array())
    {
        $params['api_key'] = $this->api_key;
        $params['auth_token'] = $this->auth_token;
        $params['target'] = $target_type; // File or folder
        $params['target_id'] = $id; // Set to ID of file or folder
        $params['tags[]'] = $tag;
        $ret_array = array();
        $data = $this->makeRequest('action=add_to_tag', $params);
        
        if ($this->_checkForError($data))
        {
            return false;
        }
        
        foreach ($data as $a)
        {
            
            switch ($a['tag'])
            {
                
                case 'STATUS' :
                    
                    $ret_array['status'] = $a['value'];
                    
                    break;
            }
        }
        
        if ($this->_debug)
        {
            echo '<h2>Tag Return</h2>';
            $this->_a($ret_array);
            print_r($a);
            
            echo '<hr />';
        }
        
        return $ret_array;
    }
    
    // Public Share (http://enabled.box.net/docs/rest#public_share)
    public function PublicShare($message, $emails, $id, $target_type, $password, $params = array())
    {
        $params['api_key'] = $this->api_key;
        $params['auth_token'] = $this->auth_token;
        $params['target'] = $target_type; // File or folder
        $params['target_id'] = $id; // Set to ID of file or folder
        $params['password'] = $password; // optional
        $params['message'] = $message;
        $params['emails'] = $emails;
        $ret_array = array();
        $data = $this->makeRequest('action=public_share', $params);
        
        if ($this->_checkForError($data))
        {
            return false;
        }
        
        foreach ($data as $a)
        {
            
            switch ($a['tag'])
            {
                
                case 'STATUS' :
                    
                    $ret_array['status'] = $a['value'];
                    
                    break;
                
                case 'PUBLIC_NAME' :
                    
                    $ret_array['public_name'] = $a['value'];
                    
                    break;
            }
        }
        
        if ($this->_debug)
        {
            echo '<h2>Public Share Return</h2>';
            $this->_a($ret_array);
            print_r($a);
            
            echo '<hr />';
        }
        
        return $ret_array;
    }
    
    // Get Friends (http://enabled.box.net/docs/rest#get_friends)
    public function GetFriends($params = array())
    {
        $params['api_key'] = $this->api_key;
        $params['auth_token'] = $this->auth_token;
        $params['params[]'] = 'nozip';
        $ret_array = array();
        $data = $this->makeRequest('action=get_friends', $params);
        
        if ($this->_checkForError($data))
        {
            return false;
        }
        
        foreach ($data as $a)
        {
            
            switch ($a['tag'])
            {
                
                case 'NAME' :
                    
                    $ret_array['name'] = $a['value'];
                    
                    break;
                
                case 'EMAIL' :
                    
                    $ret_array['email'] = $a['value'];
                    
                    break;
                
                case 'ACCEPTED' :
                    
                    $ret_array['accepted'] = $a['value'];
                    
                    break;
                
                case 'AVATAR_URL' :
                    
                    $ret_array['avatar_url'] = $a['value'];
                    
                    break;
                
                case 'ID' :
                    
                    $ret_array['id'] = $a['value'];
                    
                    break;
                
                case 'URL' :
                    
                    $ret_array['url'] = $a['value'];
                    
                    break;
                
                case 'STATUS' :
                    
                    $ret_array['status'] = $a['value'];
                    
                    break;
            }
        }
        
        if ($this->_debug)
        {
            echo '<h2>Get Friend Return</h2>';
            $this->_a($ret_array);
            print_r($a);
            
            echo '<hr />';
        }
        
        return $ret_array;
    }
    
    // Logout User
    public function Logout($params = array())
    {
        $params['api_key'] = $this->api_key;
        $params['auth_token'] = $this->auth_token;
        
        $ret_array = array();
        
        $data = $this->makeRequest('action=logout', $params);
        
        if ($this->_checkForError($data))
        {
            return false;
        }
        
        foreach ($data as $a)
        {
            
            switch ($a['tag'])
            {
                
                case 'ACTION' :
                    
                    $ret_array['logout'] = $a['value'];
                    
                    break;
            }
            
            if ($this->_debug)
            {
                echo '<h2>Logout Return</h2>';
                $this->_a($ret_array);
                print_r($a);
                
                echo '<hr />';
            }
            
            return $ret_array;
        }
    }
    
    /*
     * / / Debugging & Error Codes /
     */
    public function _checkForError($data)
    {
        if ($data[0]['attributes']['STAT'] == 'fail')
        {
            $this->_error_code = $data[1]['attributes']['CODE'];
            $this->_error_msg = $data[1]['attributes']['MSG'];
            return true;
        }
        return false;
    }

    public function isError()
    {
        if ($this->_error_msg != '')
        {
            return true;
        }
        return false;
    }

    public function getErrorMsg()
    {
        return '<p>Error: (' . $this->_error_code . ') ' . $this->_error_msg . '</p>';
    }

    public function getErrorCode()
    {
        return $this->_error_code;
    }

    public function _clearErrors()
    {
        $this->_error_code = '';
        $this->_error_msg = '';
    }

    public function setDebug($debug)
    {
        $this->_debug = $debug;
    }

    public function _a($array)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }
}
