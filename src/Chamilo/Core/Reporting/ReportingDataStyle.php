<?php
namespace Chamilo\Core\Reporting;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;

/**
 * Class stores properties of a reporting data row.
 * Note that reporting data rows are actually columns in PDF, excel, ods, etc files.
 *
 * @package reporting.lib
 * @author  Andras Zolnay
 * @see     ReportingTemplateStyle
 */
class ReportingDataStyle
{

    /**
     * Stores properies (e.g.
     * font, text color, etc.) of the data cells.
     *
     * @var ReportingDataCellStyle
     */
    private $dataCellStyle;

    /**
     * Stores properies (e.g.
     * font, text color, etc.) of the heading cell.
     *
     * @var ReportingDataCellStyle
     */
    private $headingCellStyle;

    /**
     * Row (actually column) width given relative to the page width.
     * Value is expected to be within [0..1].
     */
    private $relativeWidth;

    public function __construct(ConfigurationConsulter $configurationConsulter)
    {
        $this->headingCellStyle = new ReportingDataCellStyle();
        $this->headingCellStyle->setAlignment(
            $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'heading_cell_alignment'])
        );
        $this->headingCellStyle->setTextColor(
            $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'heading_cell_text_color'])
        );
        $this->headingCellStyle->setBackgroundColor(
            $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'heading_cell_background_color'])
        );
        $this->headingCellStyle->setBorderColor(
            $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'heading_cell_border_color'])
        );
        $this->headingCellStyle->setFont(
            [
                $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'heading_cell_font_family']),
                $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'heading_cell_font_style']),
                $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'heading_cell_font_size'])
            ]
        );

        $this->dataCellStyle = new ReportingDataCellStyle();
        $this->dataCellStyle->setAlignment(
            $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'heading_cell_alignment'])
        );
        $this->dataCellStyle->setTextColor(
            $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'data_cell_text_color'])
        );
        $this->dataCellStyle->setBackgroundColor(
            $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'data_cell_background_color'])
        );
        $this->dataCellStyle->setBorderColor(
            $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'data_cell_border_color'])
        );
        $this->dataCellStyle->setFont(
            [
                $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'data_cell_font_family']),
                $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'data_cell_font_style']),
                $configurationConsulter->getSetting(['Chamilo\Core\Reporting', 'data_cell_font_size'])
            ]
        );

        $this->relativeWidth = 0.0;
    }

    /**
     * Deep copy.
     */
    public function __clone()
    {
        $this->headingCellStyle = clone $this->headingCellStyle;
        $this->dataCellStyle = clone $this->dataCellStyle;
    }

    /**
     * @return Returns the ReportingDataCellStyle object for data cells.
     *         Usage: reporting_data_style->getDataCellStyle()->setAlignment('C');
     */
    public function getDataCellStyle()
    {
        return $this->dataCellStyle;
    }

    /**
     * @return Returns the ReportingDataCellStyle object for the heading cell.
     *         Usage: reporting_data_style->getHeadingCellStyle()->setAlignment('C');
     */
    public function getHeadingCellStyle()
    {
        return $this->headingCellStyle;
    }

    // setter and getter functions

    public function getRelativeWidth()
    {
        return $this->relativeWidth;
    }

    public function setRelativeWidth($relativeWidth)
    {
        $this->relativeWidth = $relativeWidth;
    }
}