<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Ods;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Ods;
use Chamilo\Libraries\File\Path;
use odsphpgenerator\odsTable;
use odsphpgenerator\odsTableCellEmpty;
use odsphpgenerator\odsTableCellFloat;
use odsphpgenerator\odsTableCellString;
use odsphpgenerator\odsTableRow;

require_once Path :: getInstance()->getPluginPath() . 'odsPhpGenerator/ods.php';
/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Basic extends Ods
{

    public function render()
    {
        $block = $this->get_block();
        $table = new odsTable($block->get_title());
        
        $block_content_data = $block->retrieve_data();
        
        // Column headers
        $row = new odsTableRow();
        $row->addCell(new odsTableCellEmpty());
        
        foreach ($block_content_data->get_rows() as $row_id => $row_name)
        {
            $row->addCell(new odsTableCellString($row_name));
        }
        
        $table->addRow($row);
        
        // Data rows
        $rows = array();
        
        foreach ($block_content_data->get_categories() as $category_id => $category_name)
        {
            $rows[$category_id] = new odsTableRow();
            
            $rows[$category_id]->addCell(new odsTableCellString($category_name));
            
            foreach ($block_content_data->get_rows() as $row_id => $row_name)
            {
                $cell_data = strip_tags($block_content_data->get_data_category_row($category_id, $row_id));
                if (is_numeric($cell_data))
                {
                    $rows[$category_id]->addCell(new odsTableCellFloat($cell_data));
                }
                else
                {
                    $rows[$category_id]->addCell(new odsTableCellString($cell_data));
                }
            }
            $table->addRow($rows[$category_id]);
        }
        
        return $table;
    }
}
