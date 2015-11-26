<?php
/**
 * Photobucket API Fluent interface for PHP5 Methods parent class
 * 
 * @author jhart
 * @package PBAPI
 * @copyright Copyright (c) 2008, Photobucket, Inc.
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Methods parent class
 * 
 * @package PBAPI
 */
abstract class PBAPI_Methods
{

    /**
     * 'core' PBAPI object
     * 
     * @var PBAPI
     */
    protected $core;

    /**
     * Class constructor
     * 
     * @param $core PBAPI set the main class
     */
    public function __construct($core)
    {
        $this->core = $core;
    }

    /**
     * Reset the methods objects
     */
    public function _reset()
    {
        $this->_load('base');
    }

    /**
     * Default forwarder that says method not found
     * 
     * @param $name string
     * @param $params array
     */
    public function __call($name, $params)
    {
        throw new PBAPI_Exception("Method $name not found", $this->core);
    }

    /**
     * Load a method class
     * 
     * @param $name string Method class name (one of PBAPI/Methods/*)
     * @return PBAPI_Methods
     */
    public function _load($name)
    {
        return $this->core->_loadMethodClass($name);
    }
}
