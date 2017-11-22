<?php
namespace Chamilo\Libraries\Format\Form;

/**
 *
 * @package Chamilo\Libraries\Format\Form
 */
class FormValidatorTab
{

    /**
     *
     * @var string
     */
    private $method;

    /**
     *
     * @var string
     */
    private $title;

    /**
     *
     * @param string $method
     * @param string $title
     */
    public function __construct($method, $title)
    {
        $this->method = $method;
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function get_method()
    {
        return $this->method;
    }

    /**
     *
     * @return string
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     *
     * @param string $method
     */
    public function set_method($method)
    {
        $this->method = $method;
    }

    /**
     *
     * @param string $title
     */
    public function set_title($title)
    {
        $this->title = $title;
    }
}
