<?php
namespace Chamilo\Application\Weblcms\UserExporter\Renderer;

use Chamilo\Application\Weblcms\UserExporter\UserExportRenderer;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Renders the exported user array as an excel file
 *
 * @package application\weblcms
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class ExcelUserExportRenderer implements UserExportRenderer
{

    protected ConfigurablePathBuilder $configurablePathBuilder;

    private Spreadsheet $excel;

    /**
     * Constructor
     */
    public function __construct(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->excel = new Spreadsheet();
    }

    /**
     * Renders the exported users
     *
     * @param array $headers
     * @param array $users
     *
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function render(array $headers, array $users)
    {
        $worksheet = $this->initialize_worksheet();

        $this->render_worksheet($headers, $users, $worksheet);

        return $this->save_worksheet();
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    /**
     * Initializes the worksheet
     *
     * @return \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function initialize_worksheet()
    {
        $worksheet = $this->excel->getSheet(0);
        $worksheet->setTitle('Export Users');

        return $worksheet;
    }

    /**
     * Renders the header for the user table
     *
     * @param array $headers
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     */
    protected function render_headers($headers, $worksheet)
    {
        $row = 1;

        $color = Color::COLOR_BLUE;

        $styleArray = [
            'font' => ['underline' => Font::UNDERLINE_SINGLE, 'color' => ['argb' => $color]]
        ];

        $column = 0;

        foreach ($headers as $header)
        {
            $worksheet->getColumnDimensionByColumn($column)->setWidth(50);
            $worksheet->getStyle(CellAddress::fromColumnAndRow($column, $row))->applyFromArray($styleArray);
            $worksheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), $header);

            $column ++;
        }
    }

    /**
     * Renders the worksheet
     *
     * @param array $headers
     * @param array $users
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     */
    protected function render_worksheet(array $headers, array $users, Worksheet $worksheet)
    {
        $this->render_headers($headers, $worksheet);

        $row = 2;

        foreach ($users as $user)
        {
            $column = 0;

            foreach ($user as $user_data)
            {
                $worksheet->setCellValue(CellAddress::fromColumnAndRow($column, $row), $user_data);
                $column ++;
            }

            $row ++;
        }
    }

    /**
     * Saves the worksheet to a temporary path and returns the path
     *
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    protected function save_worksheet()
    {
        $objWriter = IOFactory::createWriter($this->excel, 'Xlsx');

        $temp_dir = $this->getConfigurablePathBuilder()->getTemporaryPath() . 'excel/';

        if (!is_dir($temp_dir))
        {
            mkdir($temp_dir, 0777, true);
        }

        $filename = 'export_users_' . time();
        $file_path = $temp_dir . $filename;

        $objWriter->save($file_path);

        return $file_path;
    }
}