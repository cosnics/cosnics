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
        $this->text_color = $this->parse_color($text_color);
    }

    public function get_background_color()
    {
        return $this->background_color;
    }

    public function set_background_color($background_color)
    {
        $this->background_color = $this->parse_color($background_color);
    }

    public function get_border_color()
    {
        return $this->border_color;
    }

    public function set_border_color($border_color)
    {
        $this->border_color = $this->parse_color($border_color);
    }

    public function get_font()
    {
        return $this->font;
    }

    public function set_font($font)
    {
        $this->font = $this->parse_font($font);
    }

    /**
     *  \brief Parses R, G, B values.
     *
     *  @param $color
     *  - Can be a string: e.g. '255, 255, 255'
     *  - Can be an array: e.g. [255, 255, 255]
     *
     *  @return array: e.g. [255, 255, 255].
     */
    private function parse_color($color)
    {
        $color_array = $color;
        if (! is_array($color_array))
        {
            $color_array = explode(',', $color_array);
        }

        return array_map('intval', $color_array);
    }

    /**
     *  \brief Parses font definition.
     *
     *  @param $font
     *  - Can be a string: e.g. 'Arial, B, 12'
     *  - Can be an array: e.g. ['Arial', 'B', 10]
     *
     *  @return array: e.g. ['Arial', 'B', 10].
     */
    private function parse_font($font)
    {
        $font_array = $font;
        if (! is_array($font_array))
        {
            $font_array = explode(',', $font_array);
        }

        $result[] = trim($font_array[0]);
        $result[] = trim($font_array[1]);
        $result[] = intval($font_array[2]);

        return $result;
    }
}
?>