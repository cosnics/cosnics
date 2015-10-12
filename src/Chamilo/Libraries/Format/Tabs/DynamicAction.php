<?php
namespace Chamilo\Libraries\Format\Tabs;

class DynamicAction
{

    private $title;

    private $description;

    private $image;

    private $url;

    private $confirm;

    public function __construct($title, $description, $image, $url, $confirm = false)
    {
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->url = $url;
        $this->confirm = $confirm;
    }

    /**
     *
     * @return the $title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     *
     * @param $title the $title to set
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return the $description
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     *
     * @param $description the $description to set
     */
    public function set_description($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @return the $image
     */
    public function get_image()
    {
        return $this->image;
    }

    /**
     *
     * @param $image the $image to set
     */
    public function set_image($image)
    {
        $this->image = $image;
    }

    /**
     *
     * @return the $url
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     *
     * @param $url the $url to set
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @return the $confirm
     */
    public function get_confirm()
    {
        return $this->confirm;
    }

    /**
     *
     * @param $confirm the $confirm to set
     */
    public function set_confirm($confirm)
    {
        $this->confirm = $confirm;
    }

    /**
     *
     * @return boolean the $confirm
     */
    public function needs_confirmation()
    {
        $confirmation = $this->get_confirm();
        return $confirmation ? true : false;
    }

    public function render($is_first = false)
    {
        $html = array();
        
        if ($this->needs_confirmation())
        {
            $onclick = 'onclick = "return confirm(\'' . $this->get_confirm() . '\')"';
        }
        else
        {
            $onclick = '';
        }
        
        if ($is_first)
        {
            $html[] = '<div class="vertical_action" style="border-top: none;">';
        }
        else
        {
            $html[] = '<div class="vertical_action">';
        }
        
        $html[] = '<div class="icon">';
        $html[] = '<a href="' . $this->get_url() . '" ' . $onclick . '><img src="' . $this->get_image() . '" alt="' .
             $this->get_title() . '" title="' . $this->get_title() . '"/></a>';
        $html[] = '</div>';
        
        $title = $this->get_title();
        
        if (isset($title))
        {
            $html[] = '<div class="description">';
            $html[] = '<h4><a href="' . $this->get_url() . '" ' . $onclick . '>' . $this->get_title() . '</a></h4>';
        }
        
        $html[] = $this->get_description();
        
        if (isset($title))
        {
            $html[] = '</div>';
        }
        
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }
}
