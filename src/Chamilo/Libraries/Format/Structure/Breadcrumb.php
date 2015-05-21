<?php
namespace Chamilo\Libraries\Format\Structure;

/**
 * $Id: breadcrumb.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * 
 * @package common.html
 */
class Breadcrumb
{

    private $url;

    private $name;

    public function __construct($url, $name)
    {
        $this->url = $url;
        $this->name = $name;
    }

    public function get_url()
    {
        return $this->url;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function set_url($url)
    {
        $this->url = $url;
    }

    public function set_name($name)
    {
        $this->name = $name;
    }
}
