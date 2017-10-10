<?php
namespace Chamilo\Core\Reporting;

/**
 * Stores properies (e.g.
 * font, text color, etc.) of a reporting data cell.
 *
 * @package reporting.lib
 * @author Andras Zolnay
 * @see ReportingTemplateStyle
 */
class ReportingDataCellStyle
{

    /**
     * Text alignment.
     * Valid values: 'L', 'C', 'R', 'J'
     */
    private $alignment = 'L';

    /**
     * Data text color
     *
     * @var array(r [0..255], g [0..255], b [0..255])
     */
    private $textColor = array(0, 0, 0);

    /**
     * Data background color
     *
     * @var array(r [0..255], g [0..255], b [0..255])
     */
    private $backgroundColor = array(255, 255, 255);

    /**
     * Border color
     *
     * @var array(r [0..255], g [0..255], b [0..255])
     */
    private $borderColor = array(0, 0, 0);

    /**
     * Font specification
     *
     * @var array(family, style, size)
     *      - family: name of installed font, e.g. 'Arial'.
     *      - style: either empty or combination of 'B', 'I', 'U' or empty string.
     *      - size: font size, e.g. 10.
     */
    private $font = array('Arial', '', 10);

    // getters and setters
    public function getAlignment()
    {
        return $this->alignment;
    }

    public function setAlignment($alignment)
    {
        $this->alignment = $alignment;
    }

    public function getTextColor()
    {
        return $this->textColor;
    }

    public function setTextColor($textColor)
    {
        $this->textColor = ReportingTemplateStyle::parseColor($textColor);
    }

    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = ReportingTemplateStyle::parseColor($backgroundColor);
    }

    public function getBorderColor()
    {
        return $this->borderColor;
    }

    public function setBorderColor($borderColor)
    {
        $this->borderColor = ReportingTemplateStyle::parseColor($borderColor);
    }

    public function getFont()
    {
        return $this->font;
    }

    public function setFont($font)
    {
        $this->font = ReportingTemplateStyle::parseFont($font);
    }
}
?>