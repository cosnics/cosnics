<?php
/**
 * Photobucket API Fluent interface for PHP5 Media methods
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
 * Media methods
 * 
 * @package PBAPI
 * @subpackage Methods
 */
class PBAPI_Methods_media extends PBAPI_Methods
{

    /**
     * description
     * 
     * @param $params array
     */
    public function description($params = null)
    {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/description');
    }

    /**
     * Title
     * 
     * @param $params array
     */
    public function title($params = null)
    {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/title');
    }

    /**
     * Tag
     * 
     * @param $tagid int [optional, default=all] tag id, '' for all tags
     * @param $params array array(...)
     */
    public function tag($tagid = '', $params = null)
    {
        if (is_array($tagid) && $params == null)
        {
            $params = $tagid;
            $tagid = '';
        }
        $this->core->_appendUri('/tag/%s', $tagid);
        $this->core->_setParamList($params);
    }

    /**
     * resize
     * 
     * @param $params array
     */
    public function resize($params)
    {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/resize');
    }

    /**
     * Rotate
     * 
     * @param $params array
     */
    public function rotate($params)
    {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/rotate');
    }

    /**
     * Metadata
     * 
     * @param $params array
     */
    public function meta($params = null)
    {
        $this->core->_appendUri('/meta');
    }

    /**
     * Links
     * 
     * @param $params array
     */
    public function links($params = null)
    {
        $this->core->_appendUri('/link');
    }

    /**
     * related search
     * 
     * @param $params array
     */
    public function related($params = null)
    {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/related');
    }

    /**
     * Share
     * 
     * @param $params array
     */
    public function share($params)
    {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/share');
    }
}
