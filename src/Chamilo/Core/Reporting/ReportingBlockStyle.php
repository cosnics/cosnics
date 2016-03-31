<?php
namespace Chamilo\Core\Reporting;

/**
 * $Id: ReportingBlockStyle.php
 *
 * Class stores style of a reporting block.
 *
 * @package reporting.lib
 * @author Andras Zolnay
 *
 * @see ReportingTemplateStyle
 */
class ReportingBlockStyle
{
    /**
     *  Title text color
     *
     *  @var array(r [0..255], g [0..255], b [0..255])
     */
    private $titleTextColor = array(0, 0, 0);
    /**
     *  Title font specification
     *
     *  @var array(family, style, size)
     *
     *  - family: name of installed font, e.g. 'Arial'.
     *  - style: either empty or combination of 'B', 'I', 'U' or empty string.
     *  - size: font size, e.g. 10.
     */
    private $titleFont = array('Arial', 'B', 11);
    
    function __construct()
    {
        $this->setTitleTextColor(\Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'block_title_text_color'));
        $this->setTitleFont([\Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'block_title_font_family'),
                             \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'block_title_font_style'),
                             \Chamilo\Configuration\Configuration :: get('Chamilo\Core\Reporting', 'block_title_font_size')]);
    }
    
    public function getTitleTextColor($color)
    {
        return $this->titleTextColor;
    }

    public function setTitleTextColor($color)
    {
        $this->titleTextColor = ReportingTemplateStyle :: parseColor($color);
    }

    public function getTitleFont($font)
    {
        return $this->titleFont;
    }

    public function setTitleFont($font)
    {
        $this->titleFont = ReportingTemplateStyle :: parseFont($font);
    }
}
?>