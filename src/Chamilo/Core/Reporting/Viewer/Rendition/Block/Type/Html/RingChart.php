<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;

use Chamilo\Core\Reporting\Viewer\Chart\pChamiloImage;
use Chamilo\Libraries\File\Path;
use CpChart\Classes\pData;
use CpChart\Classes\pPie;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class RingChart extends Chart
{

    public function get_path()
    {
        $reporting_data = $this->get_block()->retrieve_data();
        
        if ($reporting_data->is_empty())
        {
            return false;
        }
        
        $md5 = md5(serialize(array('ring_chart', $reporting_data)));
        $path = $this->getFilePath($md5);
        
        if (! file_exists($path))
        {
            $reporting_data = $this->get_block()->retrieve_data();
            
            $chart_data = new pData();
            
            /* Define the absissa serie */
            $abscissa = array();
            
            foreach ($reporting_data->get_categories() as $category_id => $category_name)
            {
                $abscissa[] = trim(
                    trim(html_entity_decode(strip_tags($category_name), ENT_COMPAT, 'utf-8')), 
                    "\xC2\xA0");
            }
            
            $chart_data->addPoints($abscissa, 'Labels');
            $chart_data->setAbscissa('Labels');
            
            foreach ($reporting_data->get_rows() as $row_id => $row_name)
            {
                $data_row = array();
                
                foreach ($reporting_data->get_categories() as $category_id => $category_name)
                {
                    $data_row[] = $reporting_data->get_data_category_row($category_id, $row_id);
                }
                
                $chart_data->addPoints($data_row, $row_name);
            }
            
            $chart_data->loadPalette('spring.color');
            
            /* Create the pChart object */
            
            if (count($abscissa) > 22)
            {
                $height = 40 + (count($abscissa) * 15);
            }
            else
            {
                $height = 370;
            }
            
            $chart_canvas = new pChamiloImage(600, $height, $chart_data);
            
            /* Draw a solid background */
            $format = array('R' => 240, 'G' => 240, 'B' => 240);
            // $format = array('R' => 0, 'G' => 0, 'B' => 0);
            $chart_canvas->drawFilledRectangle(0, 0, 599, $height - 1, $format);
            
            /* Add a border to the picture */
            $format = array('R' => 255, 'G' => 255, 'B' => 255);
            $chart_canvas->drawRectangle(1, 1, 598, $height - 2, $format);
            
            /* Set the default font properties */
            $chart_canvas->setFontProperties(
                array(
                    'FontName' => Path :: getInstance()->getVendorPath() .
                         'szymach/c-pchart/src/Resources/fonts/verdana.ttf', 
                        'FontSize' => 8, 
                        'R' => 80, 
                        'G' => 80, 
                        'B' => 80));
            
            /* Create the pPie object */
            $pie_chart = new pPie($chart_canvas, $chart_data);
            
            /* Draw a simple pie chart */
            if ($height > 370)
            {
                $proposed_radius = ($height - 50) / 2;
                $radius = $proposed_radius > 180 ? 180 : $proposed_radius;
            }
            else
            {
                $radius = 160;
            }
            $pie_chart->draw2DRing(
                350, 
                $height / 2, 
                array(
                    'Border' => true, 
                    'LabelStacked' => true, 
                    'SecondPass' => true, 
                    'WriteValues' => PIE_VALUE_PERCENTAGE, 
                    'ValueR' => 0, 
                    'ValueG' => 0, 
                    'ValueB' => 0, 
                    'OuterRadius' => $radius, 
                    'InnerRadius' => $radius - 60));
            
            /* Write the legend */
            $chart_canvas->setFontProperties(
                array(
                    'FontName' => Path :: getInstance()->getVendorPath() .
                         'szymach/c-pchart/src/Resources/fonts/verdana.ttf', 
                        'FontSize' => 6));
            $chart_canvas->setShadow(TRUE, array('X' => 1, 'Y' => 1, 'R' => 0, 'G' => 0, 'B' => 0, 'Alpha' => 20));

            /* Write the legend box */
            $chart_canvas->setFontProperties(
                array(
                    'FontName' => Path :: getInstance()->getVendorPath() . 'szymach/c-pchart/src/Resources/fonts/verdana.ttf',
                    'R' => 0,
                    'G' => 0,
                    'B' => 0));
            $pie_chart->drawPieLegend(
                20, 
                26, 
                array(
                    'Style' => LEGEND_BOX, 
                    'Mode' => LEGEND_VERTICAL, 
                    'R' => 250, 
                    'G' => 250, 
                    'B' => 250, 
                    'Margin' => 5));
            
            /* Render the picture */
            $chart_canvas->render($path);
        }
        
        return $this->getUrl($md5);
    }
}
