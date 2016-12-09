<?php

/**
 * Photobucket API Fluent interface for PHP5 Base methods
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
 * Base API methods
 * 
 * @package PBAPI
 * @subpackage Methods
 */
class PBAPI_Methods_base extends PBAPI_Methods
{

    /**
     * Ping
     * 
     * @param $params array (anything)
     */
    public function ping($params = null)
    {
        if (! empty($params))
            $this->core->_setParamList($params);
        $this->core->_setUri('/ping');
    }

    /**
     * Search
     * 
     * @param $term string [optional, default=''] search term, '' for recent
     * @param $params array array(...)
     */
    public function search($term = '', $params = null)
    {
        if (is_array($term) && $params == null)
        {
            $params = $term;
            $term = '';
        }
        $this->core->_setUri('/search/%s', $term);
        if (count($params))
            $this->core->_setParamList($params);
    }

    /**
     * Featured Media
     */
    public function featured()
    {
        $this->core->_setUri('/featured');
    }

    /**
     * User
     * 
     * @param $username string [optional, default=current user token] username
     * @param $params array array(...)
     */
    public function user($username = '', $params = null)
    {
        if (is_array($username) && $params == null)
        {
            $params = $username;
            $username = '';
        }
        $this->core->_setUri('/user/%s', $username);
        $this->core->_setParamList($params);
        
        $this->_load('user');
    }

    /**
     * Album
     * 
     * @param $albumpath string album path (username/location)
     * @param $params array array(...)
     */
    public function album($albumpath, $params = null)
    {
        if (! $albumpath)
            throw new PBAPI_Exception('albumpath required', $this->core);
        
        $this->core->_setUri('/album/%s', $albumpath);
        $this->core->_setParamList($params);
        
        $this->_load('album');
    }

    /**
     * GroupAlbum
     * 
     * @param $grouppath string groupalbum path (grouphash/location)
     * @param $params array array(...)
     */
    public function group($grouppath, $params = null)
    {
        if (! $grouppath)
            throw new PBAPI_Exception('grouppath required', $this->core);
        
        $this->core->_setUri('/group/%s', $grouppath);
        $this->core->_setParamList($params);
        
        $this->_load('group');
    }

    /**
     * Media
     * 
     * @param $mediaurl string media url (http://i384.photobucket.com/albums/v000/username/location/filename.gif)
     * @param $params array array(...)
     */
    public function media($mediaurl, $params = null)
    {
        if (! $mediaurl)
            throw new PBAPI_Exception('mediaurl required', $this->core);
        
        $this->core->_setUri('/media/%s', $mediaurl);
        $this->core->_setParamList($params);
        
        $this->_load('media');
    }

    /**
     * Login
     * 
     * @param $step string [request|access] step of web login/auth process
     * @param $params array array(...)
     */
    public function login($step, $params = null)
    {
        if (! $step)
            throw new PBAPI_Exception('step required', $this->core);
        
        $this->core->_setUri('/login/%s', $step);
        $this->core->_setParamList($params);
    }

    /**
     * get accessor tokens
     * 
     * @param $params array array(...)
     */
    public function accessor($params = null)
    {
        $this->core->_setUri('/accessor');
        $this->core->_setParamList($params);
    }
}
