<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

abstract class DynamicTab
{

    private $id;

    private $name;

    private $image;

    protected $display;
    const DISPLAY_ICON = 1;
    const DISPLAY_TITLE = 2;
    const DISPLAY_ICON_AND_TITLE = 3;

    /**
     *
     * @param integer $id
     * @param string $name
     * @param string $image
     * @param int $display
     */
    public function __construct($id, $name, $image, $display = self::DISPLAY_ICON_AND_TITLE)
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
        $this->display = $display;
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
     * @return string
     */
    public function header()
    {
        $html = array();
        $html[] = '<li><a title="' . htmlentities(strip_tags($this->name)) . '" href="' . $this->get_link() . '">';
        $html[] = '<span class="category">';
        if ($this->image && $this->isIconVisible())
        {
            if (! $this->image instanceof InlineGlyph)
            {
                $html[] = '<img src="' . $this->image . '" border="0" style="vertical-align: middle; " alt="' .
                     strip_tags($this->name) . '" title="' . htmlentities(strip_tags($this->name)) . '"/>';
            }
            else
            {
                $html[] = $this->image->render();
            }
        }
        
        if ($this->image && $this->name && $this->isIconVisible() && $this->isTextVisible())
        {
            $html[] = '&nbsp;&nbsp;';
        }
        
        if ($this->name && $this->isTextVisible())
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
     * Returns whether or not the icon is visible
     * 
     * @return bool
     */
    protected function isIconVisible()
    {
        return $this->display == self::DISPLAY_ICON_AND_TITLE || $this->display == self::DISPLAY_ICON;
    }

    /**
     * Returns whether or not the text is visible
     * 
     * @return bool
     */
    protected function isTextVisible()
    {
        return $this->display == self::DISPLAY_ICON_AND_TITLE || $this->display == self::DISPLAY_TITLE;
    }

    /**
     *
     * @param string $tab_name
     * @return string
     */
    abstract public function body($isOnlyTab);
}
