<?php
namespace Chamilo\Libraries\Format\Tabs;

class DynamicContentTab extends DynamicTab
{

    private $content;

    /**
     *
     * @param integer $id
     * @param string $name
     * @param string $image
     * @param string $content
     * @param int $display
     */
    public function __construct($id, $name, $image, $content, $display = self::DISPLAY_ICON_AND_TITLE)
    {
        parent::__construct($id, $name, $image, $display);
        $this->content = $content;
    }

    /**
     *
     * @return the $content
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     *
     * @param $content the $content to set
     */
    public function set_content($content)
    {
        $this->content = $content;
    }

    public function get_link()
    {
        return '#' . $this->get_id();
    }

    /**
     *
     * @param string $tab_name
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
}
