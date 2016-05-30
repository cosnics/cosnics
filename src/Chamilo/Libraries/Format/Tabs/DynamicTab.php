<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

abstract class DynamicTab
{

    private $id;

    private $name;

    private $image;

    /**
     *
     * @param integer $id
     * @param string $name
     * @param string $image
     */
    public function __construct($id, $name, $image)
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
    }

    /**
     *
     * @return the $id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     *
     * @param $id the $id to set
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return the $name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     *
     * @param $name the $name to set
     */
    public function set_name($name)
    {
        $this->name = $name;
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

    abstract public function get_link();

    /**
     *
     * @param string $tab_name
     * @return string
     */
    public function header()
    {
        $html = array();
        $html[] = '<li><a href="' . $this->get_link() . '">';
        $html[] = '<span class="category">';
        if ($this->image)
        {
            if (! $this->image instanceof InlineGlyph)
            {
                $html[] = '<img src="' . $this->image . '" border="0" style="vertical-align: middle; " alt="' .
                     $this->name . '" title="' . $this->name . '"/>';
            }
            else
            {
                $html[] = $this->image->render();
            }
        }

        if ($this->image && $this->name)
        {
            $html[] = '&nbsp;&nbsp;';
        }

        if ($this->name)
        {
            $html[] = '<span class="title">' . $this->name . '</span>';
        }
        $html[] = '</span>';
        $html[] = '</a></li>';
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function body_header()
    {
        $html = array();

        $html[] = '<div role="tabpanel" class="tab-pane" id="' . $this->get_id() . '">';
        $html[] = '<div class="list-group-item">';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function body_footer()
    {
        $html = array();

        $html[] = '<div class="clearfix"></div>';

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string $tab_name
     * @return string
     */
    abstract public function body($isOnlyTab);
}
