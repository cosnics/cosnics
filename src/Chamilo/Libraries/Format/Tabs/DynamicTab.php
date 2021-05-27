<?php
namespace Chamilo\Libraries\Format\Tabs;

use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class DynamicTab
{
    const DISPLAY_ICON = 1;
    const DISPLAY_ICON_AND_TITLE = 3;
    const DISPLAY_TITLE = 2;

    /**
     *
     * @var integer
     */
    protected $display;

    /**
     *
     * @var string
     */
    private $id;

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
     * @param string $id
     * @param string $name
     * @param string|\Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $image
     * @param integer $display
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
     * @param boolean $isOnlyTab
     *
     * @return string
     */
    abstract public function body($isOnlyTab);

    /**
     *
     * @return string
     */
    public function body_footer()
    {
        $html = [];

        $html[] = '<div class="clearfix"></div>';

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function body_header()
    {
        $html = [];

        $html[] = '<div role="tabpanel" class="tab-pane" id="' . $this->get_id() . '">';
        $html[] = '<div class="list-group-item">';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return integer
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     *
     * @param integer $display
     */
    public function setDisplay($display)
    {
        $this->display = $display;
    }

    /**
     *
     * @return string
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     *
     * @param string $id
     */
    public function set_id($id)
    {
        $this->id = $id;
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
    abstract public function get_link();

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
    public function header()
    {
        $html = [];
        $html[] = '<li>';
        $html[] = '<a title="' . htmlentities(strip_tags($this->name)) . '" href="' . $this->get_link() . '">';
        $html[] = '<span class="category">';

        if ($this->get_image() && $this->isIconVisible())
        {
            if (!$this->get_image() instanceof InlineGlyph)
            {
                $html[] = '<img src="' . $this->get_image() . '" border="0" style="vertical-align: middle; " alt="' .
                    strip_tags($this->get_name()) . '" title="' . htmlentities(strip_tags($this->get_name())) . '"/>';
            }
            else
            {
                $html[] = $this->get_image()->render();
            }
        }

        if ($this->get_name() && $this->isTextVisible())
        {
            $html[] = '<span class="title">' . $this->get_name() . '</span>';
        }

        $html[] = '</span>';
        $html[] = '</a>';
        $html[] = '</li>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns whether or not the icon is visible
     *
     * @return boolean
     */
    protected function isIconVisible()
    {
        return $this->getDisplay() == self::DISPLAY_ICON_AND_TITLE || $this->getDisplay() == self::DISPLAY_ICON;
    }

    /**
     * Returns whether or not the text is visible
     *
     * @return boolean
     */
    protected function isTextVisible()
    {
        return $this->getDisplay() == self::DISPLAY_ICON_AND_TITLE || $this->getDisplay() == self::DISPLAY_TITLE;
    }
}
