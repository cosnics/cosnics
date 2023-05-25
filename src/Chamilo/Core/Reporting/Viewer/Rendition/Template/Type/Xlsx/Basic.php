<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Xlsx;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRenditionImplementation;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Xlsx as XlsxBlockRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Xlsx;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Translation\Translation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * @author  Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Basic extends Xlsx
{

    private $letters = [
        0 => 'A',
        1 => 'B',
        2 => 'C',
        3 => 'D',
        4 => 'E',
        5 => 'F',
        6 => 'G',
        7 => 'H',
        8 => 'I',
        9 => 'J',
        10 => 'K',
        11 => 'L',
        12 => 'M',
        13 => 'N',
        14 => 'O',
        15 => 'P',
        16 => 'Q',
        17 => 'R',
        18 => 'S',
        19 => 'T',
        20 => 'U',
        21 => 'V',
        22 => 'W',
        23 => 'X',
        24 => 'Y',
        25 => 'Z'
    ];

    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private $php_excel;

    /**
     * @return string
     */
    public function render()
    {
        $current_block_id = $this->get_context()->get_current_block();

        $this->php_excel = new Spreadsheet();

        if ($this->show_all())
        {
            $views = $this->get_context()->get_current_view();
            $specific_views = [];

            foreach ($views as $key => $view)
            {
                if ($view == static::get_format())
                {
                    $specific_views[] = $key;
                }
            }

            // Render the specific views as called from another view which can show multiple blocks
            if (count($specific_views) > 0)
            {
                if (count($specific_views) == 1)
                {
                    $current_block = $this->get_template()->get_block($specific_views[0]);
                    $file_name = Translation::get(
                            ClassnameUtilities::getInstance()->getClassnameFromObject($current_block), null,
                            ClassnameUtilities::getInstance()->getNamespaceFromObject($current_block)
                        ) . date('_Y-m-d_H-i-s') . '.xlsx';

                    BlockRenditionImplementation::launch(
                        $this, $current_block, $this->get_format(), XlsxBlockRendition::VIEW_BASIC
                    );
                }
                else
                {
                    $this->php_excel->removeSheetByIndex(0);
                    $data = [];

                    foreach ($specific_views as $key => $specific_view)
                    {
                        $block = $this->get_template()->get_block($specific_view);
                        $this->php_excel->createSheet($key);
                        $this->php_excel->setActiveSheetIndex($key);

                        BlockRenditionImplementation::launch(
                            $this, $block, $this->get_format(), XlsxBlockRendition::VIEW_BASIC
                        );
                    }

                    $file_name = Translation::get(
                            ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_template()), null,
                            ClassnameUtilities::getInstance()->getNamespaceFromObject($this->get_template())
                        ) . date('_Y-m-d_H-i-s') . '.xlsx';
                }
            }
            // No specific view was set and we are rendering everything, so render everything
            else
            {
                $this->php_excel->removeSheetByIndex(0);
                $data = [];

                foreach ($this->get_template()->get_blocks() as $key => $block)
                {
                    $this->php_excel->createSheet($key);
                    $this->php_excel->setActiveSheetIndex($key);

                    BlockRenditionImplementation::launch(
                        $this, $block, $this->get_format(), XlsxBlockRendition::VIEW_BASIC
                    );
                }

                $file_name = Translation::get(
                        ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_template()), null,
                        ClassnameUtilities::getInstance()->getNamespaceFromObject($this->get_template())
                    ) . date('_Y-m-d_H-i-s') . '.xlsx';
            }
        }
        else
        {
            $current_block_id = $this->get_context()->get_current_block();
            $current_block = $this->get_template()->get_block($current_block_id);
            $file_name = Translation::get(
                    ClassnameUtilities::getInstance()->getClassnameFromObject($current_block), null,
                    ClassnameUtilities::getInstance()->getNamespaceFromObject($current_block)
                ) . date('_Y-m-d_H-i-s') . '.xlsx';

            BlockRenditionImplementation::launch(
                $this, $current_block, $this->get_format(), XlsxBlockRendition::VIEW_BASIC
            );
        }

        $file = $this->getArchivePath() . Filesystem::create_unique_name(
                $this->getArchivePath(), $file_name
            );

        $php_excel_writer = IOFactory::createWriter($this->php_excel, 'Xlsx');
        $php_excel_writer->save($file);

        $this->php_excel->disconnectWorksheets();
        unset($this->php_excel);

        return $file;
    }

    /**
     * @return string[]
     */
    public function get_letters()
    {
        return $this->letters;
    }

    /**
     * @return Spreadsheet
     */
    public function get_php_excel()
    {
        return $this->php_excel;
    }
}
