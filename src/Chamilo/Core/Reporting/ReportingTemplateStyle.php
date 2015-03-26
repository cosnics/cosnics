<?php
namespace Chamilo\Core\Reporting;

/**
 * $Id: ReportingDataStyle.php
 *
 * Class stores properties of a reporting data row.
 * Note that reporting data rows are actually columns in PDF, excel, ods, etc files.   
 *
 * @package reporting.lib
 * @author Andras Zolnay
 *
 */
class ReportingTemplateStyle
{
    /**
     *  Paper orientation of template
     *
     *  @var 'L' or 'P'.
     */
    private $paperOrientation;
    /**
     *  Title text color
     *
     *  @var array(r [0..255], g [0..255], b [0..255])
     */
    private $headerTextColor = array(0, 0, 0);
    /**
     *  Template header font specification
     *
     *  @var array(family, style, size)
     *
     *  - family: name of installed font, e.g. 'Arial'.
     *  - style: either empty or combination of 'B', 'I', 'U' or empty string.
     *  - size: font size, e.g. 10.
     */
    private $headerFont = array('Arial', 'B', 11);
    /**
     *  Header seprator line color
     *
     *  @var array(r [0..255], g [0..255], b [0..255])
     */
    private $headerLineColor = array(0, 0, 0);
    
    function __construct()
    {
        $this->setPaperOrientation(\Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'paper_orientation'));

        $this->setHeaderTextColor(\Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'template_header_text_color'));
        $this->setHeaderFont([\Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'template_header_font_family'),
                              \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'template_header_font_style'),
                              \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'template_header_font_size')]);
        $this->setHeaderLineColor(\Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'template_header_line_color'));
        
        $this->setFooterTextColor(\Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'template_footer_text_color'));
        $this->setFooterFont([\Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'template_footer_font_family'),
                              \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'template_footer_font_style'),
                             \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'template_footer_font_size')]);
    }
    
    // setter and getter functions
    public function getPaperOrientation()
    {
        return $this->paperOrientation;
    }

    public function setPaperOrientation($paperOrientation)
    {
        $this->paperOrientation = $paperOrientation;
    }

    public function getHeaderTextColor($color)
    {
        return $this->headerTextColor;
    }

    public function setHeaderTextColor($color)
    {
        $this->headerTextColor = ReportingTemplateStyle :: parseColor($color);
    }

    public function getHeaderFont($font)
    {
        return $this->headerFont;
    }

    public function setHeaderFont($font)
    {
        $this->headerFont = ReportingTemplateStyle :: parseFont($font);
    }

    public function getHeaderLineColor($color)
    {
        return $this->headerLineColor;
    }

    public function setHeaderLineColor($color)
    {
        $this->headerLineColor = ReportingTemplateStyle :: parseColor($color);
    }

    public function getFooterTextColor($color)
    {
        return $this->footerTextColor;
    }

    public function setFooterTextColor($color)
    {
        $this->footerTextColor = ReportingTemplateStyle :: parseColor($color);
    }

    public function getFooterFont($font)
    {
        return $this->footerFont;
    }

    public function setFooterFont($font)
    {
        $this->footerFont = ReportingTemplateStyle :: parseFont($font);
    }

    /**
     *  \brief Parses font Family, Style, Size values.
     *
     *  @param $font
     *  - Can be an array of strings: e.g. ['Arial', 'B', '10']
     *
     *  @return array: e.g. ['Arial', 'B', 10]
     */
    public static function parseFont($font)
    {
        if (count($font) != 3)
        {
            throw new \Exception('Invalid font: "' . implode(', ', $font) . '".');
        }

        $result = array();

        $result[] = trim($font[0]);
        $result[] = trim($font[1]);
        $result[] = intval($font[2]);

        return $result;
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
    public static function parseColor($color)
    {
        $color_array = $color;
        if (! is_array($color_array))
        {
            $color_array = explode(',', $color_array);
        }

        if (count($color_array) != 3)
        {
            throw new \Exception('Invalid color: "' . implode(', ', $color_array) . '".');
        }

        return array_map('intval', $color_array);
    }
}
?>