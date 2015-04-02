<?php
namespace Chamilo\Libraries\Format\Form;

/**
 * $Id: foprm_tab.class.php 6 2010-03-03 9:30:20Z tristan $
 * 
 * @package common.html.formvalidator
 */
class FormValidatorTab
{

    private $method;

    private $title;

    public function __construct($method, $title)
    {
        $this->method = $method;
        $this->title = $title;
    }

    public function get_method()
    {
        return $this->method;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function set_method($method)
    {
        $this->method = $method;
    }

    public function set_title($title)
    {
        $this->title = $title;
    }
}
