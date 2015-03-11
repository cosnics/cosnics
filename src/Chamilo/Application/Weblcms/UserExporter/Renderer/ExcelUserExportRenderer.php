<?php
namespace Chamilo\Application\Weblcms\UserExporter\Renderer;

use Chamilo\Application\Weblcms\UserExporter\UserExportRenderer;
use Chamilo\Libraries\File\Path;

/**
 * Renders the exported user array as an excel file
 *
 * @package application\weblcms
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExcelUserExportRenderer implements UserExportRenderer
{

    /**
     * The reference to the PHPExcel writer
     *
     * @var \PHPExcel
     */
    private $excel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->excel = new \PHPExcel();
    }

    /**
     * Renders the exported users
     *
     * @param array $headers
     * @param array $users
     *
     * @return mixed
     */
    public function render(array $headers, array $users)
    {
        $worksheet = $this->initialize_worksheet();

        $this->render_worksheet($headers, $users, $worksheet);

        return $this->save_worksheet();
    }

    /**
     * Renders the worksheet
     *
     * @param array $headers
     * @param array $users
     * @param \PHPExcel_Worksheet $worksheet
     */
    protected function render_worksheet(array $headers, array $users, \PHPExcel_Worksheet $worksheet)
    {
        $this->render_headers($headers, $worksheet);

        $row = 2;

        foreach ($users as $user)
        {
            $column = 0;

            foreach ($user as $user_data)
            {
                $worksheet->setCellValueByColumnAndRow($column, $row, $user_data);
                $column ++;
            }

            $row ++;
        }
    }

    /**
     * Renders the header for the user table
     *
     * @param array $headers
     * @param \PHPExcel_Worksheet $worksheet
     */
    protected function render_headers($headers, $worksheet)
    {
        $row = 1;

        $color = \PHPExcel_Style_Color :: COLOR_BLUE;

        $styleArray = array(
            'font' => array('underline' => \PHPExcel_Style_Font :: UNDERLINE_SINGLE, 'color' => array('argb' => $color)));

        $column = 0;

        foreach ($headers as $header)
        {
            $worksheet->getColumnDimensionByColumn($column)->setWidth(50);
            $worksheet->getStyleByColumnAndRow($column, $row)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow($column, $row, $header);

            $column ++;
        }
    }

    /**
     * Initializes the worksheet
     *
     * @return \PHPExcel_Worksheet
     */
    protected function initialize_worksheet()
    {
        $worksheet = $this->excel->getSheet(0);
        $worksheet->setTitle('Export Users');

        return $worksheet;
    }

    /**
     * Saves the worksheet to a temporary path and returns the path
     *
     * @return string
     */
    protected function save_worksheet()
    {
        $objWriter = \PHPExcel_IOFactory :: createWriter($this->excel, 'Excel2007');

        $temp_dir = Path :: getInstance()->getTemporaryPath() . 'excel/';

        if (! is_dir($temp_dir))
        {
            mkdir($temp_dir, 0777, true);
        }

        $filename = 'export_users_' . time();
        $file_path = $temp_dir . $filename;

        $objWriter->save($file_path);

        return $file_path;
    }
}