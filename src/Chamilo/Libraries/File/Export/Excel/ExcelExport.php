<?php
namespace Chamilo\Libraries\File\Export\Excel;

use Chamilo\Libraries\File\Export\Export;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Exports data to Excel
 *
 * @package Chamilo\Libraries\File\Export\Excel
 */
class ExcelExport extends Export
{
    const EXPORT_TYPE = 'xlsx';

    /**
     * @return string
     */
    public function getType()
    {
        return self::EXPORT_TYPE;
    }

    /**
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function render_data()
    {
        $excel = new Spreadsheet();

        $data = $this->get_data();
        $letters = range('A', 'Z');

        $i = 0;
        $cell_number = 1;

        $excel->setActiveSheetIndex(0);

        foreach ($data as $block_data)
        {
            $block_title = $block_data[0];
            $block_description = $block_data[1];
            $block_content_data = $block_data[2];

            $cell_letter = 0;
            $cell_number = $cell_number + 2;
            $excel->getActiveSheet()->setCellValue(
                $letters[$cell_letter] . $cell_number, strip_tags(html_entity_decode($block_title))
            );
            $excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(60);
            $this->wrap_text($excel, $letters[$cell_letter] . $cell_number);
            ++ $cell_number;
            $excel->getActiveSheet()->setCellValue(
                $letters[$cell_letter] . $cell_number, $this->transcode_string($block_description)
            );

            if ($block_description != "")
            {
                $this->wrap_text($excel, $letters[$cell_letter] . $cell_number);
            }

            ++ $cell_number;
            // (matrix question) rows

            foreach ($block_content_data->get_rows() as $row_id => $row_name)
            {
                $cell_letter ++;
                $excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(15);
                $excel->getActiveSheet()->setCellValue(
                    $letters[$cell_letter] . $cell_number, $this->transcode_string($row_name)
                );
            }
            foreach ($block_content_data->get_categories() as $category_id => $category_name)
            {
                $cell_letter = 0;
                ++ $cell_number;
                $excel->getActiveSheet()->getColumnDimension($letters[$cell_letter])->setWidth(50);
                $excel->getActiveSheet()->setCellValue(
                    $letters[$cell_letter] . $cell_number, $this->transcode_string($category_name)
                );

                $this->wrap_text($excel, $letters[$cell_letter] . $cell_number);
                foreach ($block_content_data->get_rows() as $row_id => $row_name)
                {
                    $cell_letter ++;
                    $excel->getActiveSheet()->setCellValue(
                        $letters[$cell_letter] . $cell_number,
                        $this->transcode_string($block_content_data->get_data_category_row($category_id, $row_id))
                    );
                }
                $i ++;
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $this->get_filename() . '"');
        header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($excel, 'Xlsx');

        return $objWriter->save('php://output');
    }

    /**
     *
     * @param string $string
     *
     * @return string
     */
    static public function transcode_string($string)
    {
        $strippedAnswer = trim(strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8')));
        $strippedAnswer = str_replace(html_entity_decode('&nbsp;', ENT_COMPAT, 'UTF-8'), ' ', $strippedAnswer);
        $strippedAnswer = preg_replace('/[ \n\r\t]{2,}/', ' ', $strippedAnswer);

        return $strippedAnswer;
    }

    /**
     *
     * @param Spreadsheet $excel
     * @param string $cell
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function wrap_text($excel, $cell)
    {
        $excel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
    }
}
