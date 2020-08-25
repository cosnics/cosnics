<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Xlsx;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Xlsx;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Basic extends Xlsx
{

    public function render()
    {
        $block = $this->get_block();
        $php_excel = $this->get_context()->get_php_excel();
        
        $block_content_data = $block->retrieve_data();
        
        $column_counter = 0;
        $cell_number = 1;
        
        $title = strlen($block->get_title()) <= 31 ? $block->get_title() : StringUtilities::getInstance()->truncate(
            $block->get_title(), 
            28) . '...';

        $title = str_replace('/', '_', $title);

        $cell_letter = $this->determine_cell_letter_from_column_counter($column_counter);

        $php_excel->getActiveSheet()->setTitle($title);
        $php_excel->getActiveSheet()->getColumnDimension($cell_letter)->setWidth(60);
        
        foreach ($block_content_data->get_rows() as $row_id => $row_name)
        {
            $column_counter ++;
            $cell_letter = $this->determine_cell_letter_from_column_counter($column_counter);
            
            $php_excel->getActiveSheet()->getColumnDimension($cell_letter)->setWidth(15);
            $php_excel->getActiveSheet()->setCellValue($cell_letter . $cell_number, $this->transcode_string($row_name));
        }
        
        foreach ($block_content_data->get_categories() as $category_id => $category_name)
        {
            $column_counter = 0;
            $cell_letter = $this->determine_cell_letter_from_column_counter($column_counter);
            
            ++ $cell_number;
            
            $php_excel->getActiveSheet()->getColumnDimension($cell_letter)->setWidth(50);
            
            $php_excel->getActiveSheet()->setCellValue(
                $cell_letter . $cell_number, 
                $this->transcode_string($category_name));
            
            $this->wrap_text($php_excel, $cell_letter . $cell_number);
            
            foreach ($block_content_data->get_rows() as $row_id => $row_name)
            {
                $column_counter ++;
                $cell_letter = $this->determine_cell_letter_from_column_counter($column_counter);
                
                $php_excel->getActiveSheet()->setCellValue(
                    $cell_letter . $cell_number, 
                    $this->transcode_string($block_content_data->get_data_category_row($category_id, $row_id)));
            }
        }
        
        $cell_letter = $this->determine_cell_letter_from_column_counter($column_counter);
        $this->wrap_text($php_excel, $cell_letter . $cell_number);
        $cell_number = $cell_number + 2;
        
        $data = $this->get_block()->retrieve_data();
    }

    public function wrap_text($php_excel, $cell)
    {
        $php_excel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
    }

    public function transcode_string($string)
    {
        $stripped_answer = trim(strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8')));
        $stripped_answer = str_replace(html_entity_decode('&nbsp;', ENT_COMPAT, 'UTF-8'), ' ', $stripped_answer);
        return preg_replace('/[ \n\r\t]{2,}/', ' ', $stripped_answer);
    }

    /**
     * Determines the cell letter from a given column counter
     * 
     * @param $column_counter
     * @return string
     */
    protected function determine_cell_letter_from_column_counter($column_counter)
    {
        $letters = $this->get_context()->get_letters();
        
        if ($column_counter < 26)
        {
            return $letters[$column_counter];
        }
        
        else
        {
            return $letters[(($column_counter + 1) / 26) - 1] . $letters[(($column_counter + 1) % 26) - 1];
        }
    }
}
