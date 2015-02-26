<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Ods;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRenditionImplementation;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Ods as OdsBlockRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Ods;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use odsphpgenerator\ods;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Basic extends Ods
{

    /**
     *
     * @return string
     */
    public function render()
    {
        $ods = new ods();
        
        if ($this->show_all())
        {
            $views = $this->get_context()->get_current_view();
            $specific_views = array();
            
            foreach ($views as $key => $view)
            {
                if ($view == static :: get_format())
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
                    $file_name = Translation :: get(
                        ClassnameUtilities :: getInstance()->getClassnameFromObject($current_block), 
                        null, 
                        ClassnameUtilities :: getInstance()->getNamespaceFromObject($current_block)) .
                         date('_Y-m-d_H-i-s') . '.ods';
                    
                    $ods->addTable(
                        BlockRenditionImplementation :: launch(
                            $this, 
                            $current_block, 
                            $this->get_format(), 
                            OdsBlockRendition :: VIEW_BASIC));
                }
                else
                {
                    $data = array();
                    
                    foreach ($specific_views as $specific_view)
                    {
                        $block = $this->get_template()->get_block($specific_view);
                        $ods->addTable(
                            BlockRenditionImplementation :: launch(
                                $this, 
                                $block, 
                                $this->get_format(), 
                                OdsBlockRendition :: VIEW_BASIC));
                    }
                    
                    $file_name = Translation :: get(
                        ClassnameUtilities :: getInstance()->getClassnameFromObject($this->get_template()), 
                        null, 
                        ClassnameUtilities :: getInstance()->getNamespaceFromObject($this->get_template())) .
                         date('_Y-m-d_H-i-s') . '.ods';
                }
            }
            // No specific view was set and we are rendering everything, so render everything
            else
            {
                $data = array();
                
                foreach ($this->get_template()->get_blocks() as $key => $block)
                {
                    $ods->addTable(
                        BlockRenditionImplementation :: launch(
                            $this, 
                            $block, 
                            $this->get_format(), 
                            OdsBlockRendition :: VIEW_BASIC));
                }
                
                $file_name = Translation :: get(
                    ClassnameUtilities :: getInstance()->getClassnameFromObject($this->get_template()), 
                    null, 
                    ClassnameUtilities :: getInstance()->getNamespaceFromObject($this->get_template())) .
                     date('_Y-m-d_H-i-s') . '.ods';
            }
        }
        else
        {
            $current_block_id = $this->get_context()->get_current_block();
            $current_block = $this->get_template()->get_block($current_block_id);
            $file_name = Translation :: get(
                ClassnameUtilities :: getInstance()->getClassnameFromObject($current_block), 
                null, 
                ClassnameUtilities :: getInstance()->getNamespaceFromObject($current_block)) . date('_Y-m-d_H-i-s') .
                 '.ods';
            
            $ods->addTable(
                BlockRenditionImplementation :: launch(
                    $this, 
                    $current_block, 
                    $this->get_format(), 
                    OdsBlockRendition :: VIEW_BASIC));
        }
        
        $file = Path :: getInstance()->getArchivePath() . Filesystem :: create_unique_name(
            Path :: getInstance()->getArchivePath(), 
            $file_name);
        
        $ods->genOdsFile($file);
        return $file;
    }
}
