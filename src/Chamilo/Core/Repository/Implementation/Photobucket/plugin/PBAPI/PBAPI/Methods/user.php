<?php
/**
 * Photobucket API Fluent interface for PHP5 User methods
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
 * User Methods
 * 
 * @package PBAPI
 * @subpackage Methods
 */
class PBAPI_Methods_user extends PBAPI_Methods
{

    /**
     * search
     * 
     * @param $params array
     */
    public function search($term = null, $params = null)
    {
        $this->core->_setParamList($params);
        if ($term)
        {
            $this->core->_appendUri('/search/' . $term);
        }
        else
        {
            $this->core->_appendUri('/search');
        }
    }

    /**
     * URLs
     * 
     * @param $params array
     */
    public function url($params = null)
    {
        $this->core->_appendUri('/url');
    }

    /**
     * Contacts
     * 
     * @param $params array
     */
    public function contact($params = null)
    {
        $this->core->_appendUri('/contact');
    }

    /**
     * upload options
     * 
     * @param $params array
     */
    public function uploadoption($params = null)
    {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/uploadoption');
    }

    /**
     * get Tags for a user
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

    /**
     * get Favorites for a user
     * 
     * @param $favid int id of a single favorite
     * @param $params array
     */
    public function favorite($favid = '', $params = null)
    {
        if (is_array($favid) && $params == null)
        {
            $params = $favid;
            $favid = '';
        }
        $this->core->_appendUri('/favorite/%s', $favid);
        $this->core->_setParamList($params);
    }
}
