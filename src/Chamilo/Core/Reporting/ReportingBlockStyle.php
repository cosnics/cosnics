<?php
namespace Chamilo\Core\Reporting;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;

/**
 * Class stores style of a reporting block.
 *
 * @package reporting.lib
 * @author  Andras Zolnay
 * @see     ReportingTemplateStyle
 */
class ReportingBlockStyle
{

    /**
     * Title font specification
     *
     * @var array(family, style, size)
     *      - family: name of installed font, e.g. 'Arial'.
     *      - style: either empty or combination of 'B', 'I', 'U' or empty string.
     *      - size: font size, e.g. 10.
     */
    private $titleFont = ['Arial', 'B', 11];

    /**
     * Title text color
     *
     * @var array(r [0..255], g [0..255], b [0..255])
     */
    private $titleTextColor = [0, 0, 0];

    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->setTitleTextColor(
            $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'block_title_text_color'])
        );
        $this->setTitleFont(
            [
                $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'block_title_font_family']),
                $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'block_title_font_style']),
                $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'block_title_font_size'])
            ]
        );
    }

    public function getTitleFont()
    {
        return $this->titleFont;
    }

    public function getTitleTextColor()
    {
        return $this->titleTextColor;
    }

    public function setTitleFont($font)
    {
        $this->titleFont = ReportingTemplateStyle::parseFont($font);
    }

    public function setTitleTextColor($color)
    {
        $this->titleTextColor = ReportingTemplateStyle::parseColor($color);
    }
}