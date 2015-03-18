<?php
namespace Chamilo\Core\Reporting;

/**
 * $Id: ReportingDataCellStyle.php
 *
 * Stores properies (e.g. font, text color, etc.) of a reporting data cell.   
 *
 * @package reporting.lib
 * @author Andras Zolnay
 */
class ReportingDataCellStyle
{
    /**
     *  Text alignment.
     *
     *  Valid values: 'L', 'C', 'R', 'J'
     */
    private $alignment = 'L';
    /**
     *  Data text color
     *
     *  @var array(r [0..255], g [0..255], b [0..255])
     */
    private $text_color = array(0, 0, 0);
    /**
     *  Data background color
     *
     *  @var array(r [0..255], g [0..255], b [0..255])
     */
    private $background_color = array(255, 255, 255);
    /**
     *  Border color
     *
     *  @var array(r [0..255], g [0..255], b [0..255])
     */
    private $border_color = array(0, 0, 0);
    /**
     *  Font specification
     *
     *  @var array(family, style, size)
     *
     *  - family: name of installed font, e.g. 'Arial'.
     *  - style: either empty or combination of 'B', 'I', 'U' or empty string.
     *  - size: font size, e.g. 10.
     */
    private $font = array('Arial', '', 10);
   

    // getters and setters
    public function get_alignment()
    {
        return $this->alignment;
    }

    public function set_alignment($alignment)
    {
        $this->alignment = $alignment;
    }

    public function get_text_color()
    {
        return $this->text_color;
    }

    public function set_text_color($text_color)
    {
        $this->text_color = $text_color;
    }

    public function get_background_color()
    {
        return $this->background_color;
    }

    public function set_background_color($background_color)
    {
        $this->background_color = $background_color;
    }

    public function get_border_color()
    {
        return $this->border_color;
    }

    public function set_border_color($border_color)
    {
        $this->border_color = $border_color;
    }

    public function get_font()
    {
        return $this->font;
    }

    public function set_font($font)
    {
        $this->font = $font;
    }
}
?>