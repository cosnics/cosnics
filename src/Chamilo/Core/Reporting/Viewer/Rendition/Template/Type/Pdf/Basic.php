<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Pdf;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRenditionImplementation;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Pdf as PdfBlockRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Pdf;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use PDF_MC_Table;

/**
 *
 * @author Andras Zolnay
 * @package reporting.viewer
 * @see For details on PDF report customization see Chamilo/Core/Reporting/ReportingDataStyle.php.
 */
class Basic extends Pdf
{

    /**
     *
     * @var \PDF_MC_Table
     */
    private $pdf_mc_table;

    public function render()
    {
        require_once Path::getInstance()->getPluginPath() . 'fpdf/mc_table.php';
        
        $this->pdf_mc_table = new PDF_MC_Table($this->get_template()->getStyle()->getPaperOrientation(), 'mm', 'A4');
        
        $this->pdf_mc_table->SetMargins(10, 5);
        $this->pdf_mc_table->SetAutoPageBreak(true, 17);
        
        $this->pdf_mc_table->SetHeader(
            Theme::getInstance()->getCommonImagePath('LogoReport', 'png', false), 
            Translation::get(
                ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_template()), 
                null, 
                ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_template())), 
            date('d-m-Y'), 
            $this->get_template()->getStyle());
        
        $this->pdf_mc_table->SetFooter(Translation::get("PdfReportFooter"), $this->get_template()->getStyle());
        
        $this->pdf_mc_table->AddPage();
        
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
                        ClassnameUtilities::getInstance()->getClassnameFromObject($current_block)) . date('_Y-m-d_H-i-s') .
                         '.pdf';
                    
                    $data = BlockRenditionImplementation::launch(
                        $this, 
                        $current_block, 
                        $this->get_format(), 
                        PdfBlockRendition::VIEW_DEFAULT);
                }
                else
                {
                    $data = array();
                    
                    foreach ($specific_views as $specific_view)
                    {
                        $block = $this->get_template()->get_block($specific_view);
                        $rendered_block = BlockRenditionImplementation::launch(
                            $this, 
                            $block, 
                            $this->get_format(), 
                            PdfBlockRendition::VIEW_DEFAULT);
                        $data[] = array(
                            Translation::get(
                                ClassnameUtilities::getInstance()->getClassnameFromObject($block), 
                                null, 
                                ClassnameUtilities::getInstance()->getNamespaceFromObject($block)));
                        $data[] = array();
                        $data = array_merge($data, $rendered_block);
                        $data[] = array();
                    }
                    
                    $file_name = Translation::get(
                        ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_template()), 
                        null, 
                        ClassnameUtilities::getInstance()->getNamespaceFromObject($this->get_template())) .
                         date('_Y-m-d_H-i-s') . '.pdf';
                }
            }
            // No specific view was set and we are rendering everything, so render everything
            else
            {
                $data = array();
                
                foreach ($this->get_template()->get_blocks() as $key => $block)
                {
                    $rendered_block = BlockRenditionImplementation::launch(
                        $this, 
                        $block, 
                        $this->get_format(), 
                        PdfBlockRendition::VIEW_BASIC);
                    $data[] = array(
                        Translation::get(
                            ClassnameUtilities::getInstance()->getClassnameFromObject($block), 
                            null, 
                            ClassnameUtilities::getInstance()->getClassnameFromObject($block)));
                    $data[] = array();
                    $data = array_merge($data, $rendered_block);
                    $data[] = array();
                }
                
                $file_name = Translation::get(
                    ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_template()), 
                    null, 
                    ClassnameUtilities::getInstance()->getClassnameFromObject($this->get_template())) .
                     date('_Y-m-d_H-i-s') . '.pdf';
            }
        }
        else
        {
            $current_block_id = $this->get_context()->get_current_block();
            $current_block = $this->get_template()->get_block($current_block_id);
            $file_name = Translation::get(
                ClassnameUtilities::getInstance()->getClassnameFromObject($current_block), 
                null, 
                ClassnameUtilities::getInstance()->getNamespaceFromObject($current_block)) . date('_Y-m-d_H-i-s') . '.pdf';
            
            $data = BlockRenditionImplementation::launch(
                $this, 
                $current_block, 
                $this->get_format(), 
                PdfBlockRendition::VIEW_DEFAULT);
        }
        
        $file = Path::getInstance()->getArchivePath() .
             Filesystem::create_unique_name(Path::getInstance()->getArchivePath(), $file_name);
        
        $handle = fopen($file, 'a+');
        if (! fwrite($handle, $this->pdf_mc_table->Output('', 'S')))
        {
            return false;
        }
        
        fclose($handle);
        
        unset($this->pdf_mc_table);
        
        return $file;
    }

    /**
     *
     * @return \PDF_MC_Table
     */
    public function get_pdf_mc_table()
    {
        return $this->pdf_mc_table;
    }
}
