<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Csv;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRenditionImplementation;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Csv as CsvBlockRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Csv;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;

/**
 * @author  Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Basic extends Csv
{

    public function render()
    {
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
                        ) . date('_Y-m-d_H-i-s') . '.csv';

                    $data = BlockRenditionImplementation::launch(
                        $this, $current_block, $this->get_format(), CsvBlockRendition::VIEW_BASIC
                    );
                }
                else
                {
                    $data = [];

                    foreach ($specific_views as $specific_view)
                    {
                        $block = $this->get_template()->get_block($specific_view);
                        $rendered_block = BlockRenditionImplementation::launch(
                            $this, $block, $this->get_format(), CsvBlockRendition::VIEW_BASIC
                        );
                        $data[] = [
                            Translation::get(
                                ClassnameUtilities::getInstance()->getClassnameFromObject($block), null,
                                ClassnameUtilities::getInstance()->getNamespaceFromObject($block)
                            )
                        ];
                        $data[] = [];
                        $data = array_merge($data, $rendered_block);
                        $data[] = [];
                    }

                    $file_name = Translation::get(
                            ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_template()), null,
                            ClassnameUtilities::getInstance()->getNamespaceFromObject($this->get_template())
                        ) . date('_Y-m-d_H-i-s') . '.csv';
                }
            }
            // No specific view was set and we are rendering everything, so render everything
            else
            {
                $data = [];

                foreach ($this->get_template()->get_blocks() as $key => $block)
                {
                    $rendered_block = BlockRenditionImplementation::launch(
                        $this, $block, $this->get_format(), CsvBlockRendition::VIEW_BASIC
                    );
                    $data[] = [
                        Translation::get(
                            ClassnameUtilities::getInstance()->getClassnameFromObject($block), null,
                            ClassnameUtilities::getInstance()->getNamespaceFromObject($block)
                        )
                    ];
                    $data[] = [];
                    $data = array_merge($data, $rendered_block);
                    $data[] = [];
                }

                $file_name = Translation::get(
                        ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_template()), null,
                        ClassnameUtilities::getInstance()->getNamespaceFromObject($this->get_template())
                    ) . date('_Y-m-d_H-i-s') . '.csv';
            }
        }
        else
        {
            $current_block_id = $this->get_context()->get_current_block();
            $current_block = $this->get_template()->get_block($current_block_id);
            $file_name = Translation::get(
                    ClassnameUtilities::getInstance()->getClassnameFromObject($current_block), null,
                    ClassnameUtilities::getInstance()->getNamespaceFromObject($current_block)
                ) . date('_Y-m-d_H-i-s') . '.csv';

            $data = BlockRenditionImplementation::launch(
                $this, $current_block, $this->get_format(), CsvBlockRendition::VIEW_BASIC
            );
        }

        $file = $this->getArchivePath() . $this->getFilesystemTools()->createUniqueName(
                $this->getArchivePath(), $file_name
            );

        $handle = fopen($file, 'a+');

        foreach ($data as $row)
        {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $file;
    }
}
