<?php
/**
 * Photobucket API Fluent interface for PHP5 GroupAlbum methods
 * 
 * @author Photobucket
 * @package PBAPI
 * @subpackage Methods
 * @copyright Copyright Copyright (c) 2008, Photobucket, Inc.
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Load Methods parent
 */
require_once dirname(__FILE__) . '/../Methods.php';

/**
 * GroupAlbum submethods
 * 
 * @package PBAPI
 * @subpackage Methods
 */
class PBAPI_Methods_group extends PBAPI_Methods
{

    /**
     * Upload File
     * 
     * @param $params array
     */
    public function upload($params)
    {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/upload');
    }

    /**
     * Privacy
     * 
     * @param $params array
     */
    public function privacy($params = null)
    {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/privacy');
    }

    /**
     * Vanity
     * 
     * @param $params array
     */
    public function vanity($params = null)
    {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/vanity');
    }

    /**
     * info
     * 
     * @param $params array
     */
    public function info($params = null)
    {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/info');
    }

    /**
     * contributors
     * 
     * @param $username string
     * @param $params array
     */
    public function contributor($username = '', $params = null)
    {
        if (is_array($username) && $params == null)
        {
            $params = $username;
            $username = '';
        }
        $this->core->_appendUri('/contributor/%s', $username);
        $this->core->_setParamList($params);
    }

    /**
     * get Tags for a group
     * 
     * @param $tagname string name of a single tag to get media for
     * @param $params array
     */
    public function tag($tagname = '', $params = null)
    {
        if (is_array($tagname) && $params == null)
        {
            $params = $tagname;
            $tagname = '';
        }
        $this->core->_appendUri('/tag/%s', $tagname);
        $this->core->_setParamList($params);
    }
}
