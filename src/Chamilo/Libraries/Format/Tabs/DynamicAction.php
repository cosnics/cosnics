<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DynamicAction
{

    /**
     *
     * @var string
     */
    private $title;

    /**
     *
     * @var string
     */
    private $description;

    /**
     *
     * @var string
     */
    private $image;

    /**
     *
     * @var string
     */
    private $url;

    /**
     *
     * @var boolean
     */
    private $confirm;

    /**
     *
     * @param string $title
     * @param string $description
     * @param string|\Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $image
     * @param string $url
     * @param boolean $confirm
     */
    public function __construct($title, $description, $image, $url = null, $confirm = false)
    {
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->url = $url;
        $this->confirm = $confirm;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $html = [];

        if ($this->needs_confirmation())
        {
            $onclick = 'onclick = "return confirm(\'' . $this->get_confirm() . '\')"';
        }
        else
        {
            $onclick = '';
        }

        $html[] = '<div class="list-group-item vertical-action">';

        $html[] = '<div class="pull-left icon">';
        $html[] = '<a href="' . $this->get_url() . '" ' . $onclick . '>';

        if ($this->get_image() instanceof InlineGlyph)
        {
            $html[] = $this->get_image()->render();
        }
        else
        {
            $html[] = '<img src="' . $this->get_image() . '" alt="' . $this->get_title() . '" title="' .
                htmlentities($this->get_title()) . '"/>';
        }

        $html[] = '</a>';
        $html[] = '</div>';

        $html[] = '<div class="pull-left">';

        $title = $this->get_title();

        if (isset($title))
        {
            $html[] = '<h5 class="list-group-item-heading"><a href="' . $this->get_url() . '" ' . $onclick . '>' .
                $this->get_title() . '</a></h5>';
        }

        $html[] = '<p class="list-group-item-text">' . $this->get_description() . '</p>';
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return boolean
     */
    public function get_confirm()
    {
        return $this->confirm;
    }

    /**
     *
     * @param boolean $confirm
     */
    public function set_confirm($confirm)
    {
        $this->confirm = $confirm;
    }

    /**
     *
     * @return string
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     *
     * @param string $description
     */
    public function set_description($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @return string
     */
    public function get_image()
    {
        return $this->image;
    }

    /**
     *
     * @param string $image
     */
    public function set_image($image)
    {
        $this->image = $image;
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
     * @param string $title
     */
    public function set_title($title)
    {
        $this->title = $title;
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
     * @param string $url
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    /**
     *
     * @return boolean
     */
    public function needs_confirmation()
    {
        $confirmation = $this->get_confirm();

        return $confirmation ? true : false;
    }
}
