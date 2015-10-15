<?php
namespace Chamilo\Libraries\Format\Structure;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 */
class Breadcrumb
{

    /**
     *
     * @var string
     */
    private $url;

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var string
     */
    private $image;

    /**
     *
     * @param string $url
     * @param string $name
     * @param string $image
     */
    public function __construct($url, $name, $image)
    {
        $this->url = $url;
        $this->name = $name;
        $this->image = $image;
    }

    /**
     *
     * @return string
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     *
     * @param string $url
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @param string $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     *
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
}
