<?php
/**
 * Photobucket API Fluent interface for PHP5 Album methods
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
 * Album submethods
 * 
 * @package PBAPI
 * @subpackage Methods
 */
class PBAPI_Methods_album extends PBAPI_Methods
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
}
