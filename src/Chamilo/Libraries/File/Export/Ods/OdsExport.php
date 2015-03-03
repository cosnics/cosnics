<?php
namespace Chamilo\Libraries\File\Export\Ods;

use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Request;
use odsphpgenerator\ods;
use odsphpgenerator\odsTable;
use odsphpgenerator\odsTableCellEmpty;
use odsphpgenerator\odsTableCellFloat;
use odsphpgenerator\odsTableCellString;
use odsphpgenerator\odsTableRow;

/**
 * Export reporting blocks to Opendocument spreadsheet format (ODS) using odsPhpGenerator by the "Lapinator"
 * http://odsphpgenerator.lapinator.net/
 *
 * @package common.export.ods
 * @author Joris Willems<joris.willems@gmail.com>
 */
require_once __DIR__ . '/../export.class.php';
require_once Path :: getInstance()->getPluginPath() . 'odsPhpGenerator/ods.php';

/**
 * Exports data to Ods
 */
class OdsExport extends Export
{
    const EXPORT_TYPE = 'ods';

    public function render_data()
    {
        // Create Ods object
        $ods = new ods();

        // Create table named 'Cells'
        $this->table = new odsTable('Cells');

        $data = $this->get_data();
        if (is_array($data))
        {
            // .. ?
        }
        else
        {
            if (Request :: get(\Chamilo\Core\Reporting\Manager :: PARAM_REPORTING_BLOCK_ID) == null)
            {
                // Export of a complete template
                $blocks = $data->get_reporting_blocks();

                foreach ($blocks as $block)
                {
                    $this->add_block($block);

                    $row = new odsTableRow();
                    $this->table->addRow($row);
                }
            }
            else
            {
                $blocks = $data->get_reporting_blocks();
                foreach ($blocks as $block)
                {
                    // Export of a single block of a template
                    if ($block->get_id() == Request :: get(\Chamilo\Core\Reporting\Manager :: PARAM_REPORTING_BLOCK_ID))
                    {
                        $this->add_block($block);
                    }
                }
            }
        }

        $ods->addTable($this->table);

        // Download the file
        $ods->downloadOdsFile($this->get_filename());
        exit();
        return true;
    }

    function transcode_string($string)
    {
        $stripped_answer = trim(strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8')));
        $stripped_answer = str_replace(html_entity_decode('&nbsp;', ENT_COMPAT, 'UTF-8'), ' ', $stripped_answer);
        $stripped_answer = preg_replace('/[ \n\r\t]{2,}/', ' ', $stripped_answer);
        return $stripped_answer;
    }

    function get_type()
    {
        return self :: EXPORT_TYPE;
    }

    function add_block($block)
    {
        $block_title = $block->get_title();
        $block_content_data = $block->retrieve_data();

        // Titel
        $row = new odsTableRow();
        $row->addCell(new odsTableCellString($block_title));
        $this->table->addRow($row);

        // Column headers
        $row = new odsTableRow();
        $row->addCell(new odsTableCellEmpty());
        foreach ($block_content_data->get_rows() as $row_id => $row_name)
        {
            $row->addCell(new odsTableCellString($row_name));
        }
        $this->table->addRow($row);

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
            $this->table->addRow($rows[$category_id]);
        }
    }
}
?>