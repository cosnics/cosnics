<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Xml;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRenditionImplementation;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Csv as CsvBlockRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Xml;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Basic extends Xml
{

    public function render()
    {
        if ($this->show_all())
        {
            $views = $this->get_context()->get_current_view();
            $specific_views = array();
            
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
                        ClassnameUtilities::getInstance()->getClassnameFromObject($current_block), 
                        null, 
                        ClassnameUtilities::getInstance()->getNamespaceFromObject($current_block)) . date('_Y-m-d_H-i-s') .
                         '.xml';
                    
                    $data = BlockRenditionImplementation::launch(
                        $this, 
                        $current_block, 
                        $this->get_format(), 
                        CsvBlockRendition::VIEW_BASIC);
                }
                else
                {
                    $data = array();
                    
                    foreach ($specific_views as $specific_view)
                    {
                        $block = $this->get_template()->get_block($specific_view);
                        $data[ClassnameUtilities::getInstance()->getClassnameFromObject($block)] = BlockRenditionImplementation::launch(
                            $this, 
                            $block, 
                            $this->get_format(), 
                            CsvBlockRendition::VIEW_BASIC);
                    }
                    
                    $file_name = Translation::get(
                        ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_template()), 
                        null, 
                        ClassnameUtilities::getInstance()->getNamespaceFromObject($this->get_template())) .
                         date('_Y-m-d_H-i-s') . '.xml';
                }
            }
            // No specific view was set and we are rendering everything, so render everything
            else
            {
                $data = array();
                
                foreach ($this->get_template()->get_blocks() as $key => $block)
                {
                    $data[ClassnameUtilities::getInstance()->getClassnameFromObject($block)] = BlockRenditionImplementation::launch(
                        $this, 
                        $block, 
                        $this->get_format(), 
                        CsvBlockRendition::VIEW_BASIC);
                }
                
                $file_name = Translation::get(
                    ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_template()), 
                    null, 
                    ClassnameUtilities::getInstance()->getNamespaceFromObject($this->get_template())) .
                     date('_Y-m-d_H-i-s') . '.xml';
            }
        }
        else
        {
            $current_block_id = $this->determine_current_block_id();
            $current_block = $this->get_template()->get_block($current_block_id);
            $file_name = Translation::get(
                ClassnameUtilities::getInstance()->getClassnameFromObject($current_block), 
                null, 
                ClassnameUtilities::getInstance()->getNamespaceFromObject($current_block)) . date('_Y-m-d_H-i-s');
            $data = BlockRenditionImplementation::launch(
                $this, 
                $current_block, 
                $this->get_format(), 
                CsvBlockRendition::VIEW_BASIC);
        }
        
        $export = Export::factory('xml', $data);
        $export->set_filename($file_name);
        return $export->write_to_file();
    }
}
