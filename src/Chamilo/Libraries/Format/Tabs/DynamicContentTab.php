<?php
namespace Chamilo\Libraries\Format\Tabs;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DynamicContentTab extends DynamicTab
{

    /**
     *
     * @var string
     */
    private $content;

    /**
     *
     * @param integer $id
     * @param string $name
     * @param string|\Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph $image
     * @param string $content
     * @param integer $display
     */
    public function __construct($id, $name, $image, $content, $display = self::DISPLAY_ICON_AND_TITLE)
    {
        parent::__construct($id, $name, $image, $display);
        $this->content = $content;
    }

    /**
     * @param boolean $isOnlyTab
     *
     * @return string
     */
    public function body($isOnlyTab = false)
    {
        $html = array();
        $html[] = $this->body_header();
        $html[] = $this->content;
        $html[] = $this->body_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     *
     * @param string $content
     */
    public function set_content($content)
    {
        $this->content = $content;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Tabs\DynamicTab::get_link()
     */
    public function get_link()
    {
        return '#' . $this->get_id();
    }
}
