<?php
namespace Chamilo\Libraries\Format\Structure;

/**
 * A standard panel containing HTML content.
 * 
 * @author Tom Goethals
 */
class Panel
{

    private $id;

    private $content;

    private $width;

    private $unit;

    /**
     *
     * @param integer $id The id of this panel
     * @param string $content The HTML content for this panel
     * @param integer $width The starting width of this panel, in the same unit as $unit
     * @param string $unit The unit for the width, default %
     */
    public function __construct($id, $content, $width, $unit = '%')
    {
        $this->id = $id;
        $this->content = $content;
        $this->width = $width;
        $this->unit = $unit;
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

    /**
     *
     * @return the $unit
     */
    public function get_unit()
    {
        return $this->unit;
    }

    /**
     *
     * @param $unit the $unit to set
     */
    public function set_unit($unit)
    {
        $this->unit = $unit;
    }

    /**
     *
     * @return the $width
     */
    public function get_width()
    {
        return $this->width;
    }

    /**
     *
     * @param $width the $width to set
     */
    public function set_width($width)
    {
        $this->width = $width;
    }

    /**
     * Creates the header HTML.
     * $last indicates whether or not this is the last panel in the sequence.
     * The last panel should not get the "resizable" css.
     * 
     * @param boolean $last
     * @return string
     */
    protected function body_header($last)
    {
        $html = array();
        $class = 'class="ui-widget-content2"';
        if ($last)
            $class = '';
        $html[] = '<div ' . $class . ' id="' . $this->get_id() . '" style="width: ' . $this->get_width() .
             $this->get_unit() . '; float: left;">';
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    protected function body_footer()
    {
        $html = array();
        $html[] = '</div>';
        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the HTML for the panel and its content.
     * 
     * @param boolean $last = false
     * @return string
     */
    public function body($last = false)
    {
        $html = array();
        $html[] = $this->body_header($last);
        $html[] = $this->content;
        $html[] = $this->body_footer();
        return implode(PHP_EOL, $html);
    }
}
