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
     */
    public function __construct($id, $name, $image, $content)
    {
        parent :: __construct($id, $name, $image);
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
    public function body()
    {
        $html = array();
        $html[] = $this->body_header();
        $html[] = $this->content;
        $html[] = $this->body_footer();
        return implode("\n", $html);
    }
}
