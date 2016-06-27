<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;

use Chamilo\Core\Reporting\Viewer\Chart\pChamiloImage;
use Chamilo\Libraries\File\Path;
use CpChart\Classes\pData;
use CpChart\Classes\pRadar;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class RadarChart extends Chart
{

    public function get_path()
    {
        $reporting_data = $this->get_block()->retrieve_data();
        
        if ($reporting_data->is_empty())
        {
            return false;
        }
        
        $md5 = md5(serialize(array('radar_chart', $reporting_data)));
        $path = $this->getFilePath($md5);
        
        if (! file_exists($path))
        {
            $number_of_rows = count($reporting_data->get_rows());
            $number_of_categories = count($reporting_data->get_categories());
            $number_of_bars = $number_of_rows * $number_of_categories;
            
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
                    $value = $reporting_data->get_data_category_row($category_id, $row_id);
                    $data_row[] = is_null($value) ? VOID : $value;
                }
                
                $chart_data->addPoints($data_row, $row_name);
            }
            
            $chart_data->loadPalette('spring.color');
            
            if ($number_of_rows > 1)
            {
                if ($number_of_rows > 24)
                {
                    $height = (13 * $number_of_rows) + 52;
                }
                else
                {
                    $height = 370;
                }
                $graph_area_left = 70;
            }
            else
            {
                $height = 370;
                $graph_area_left = 20;
            }
            
            /* Create the pChart object */
            $chart_canvas = new pChamiloImage(600, $height, $chart_data);
            
            /* Draw a solid background */
            $format = array('R' => 240, 'G' => 240, 'B' => 240);
            $chart_canvas->drawFilledRectangle(0, 0, 599, $height - 1, $format);
            
            /* Add a border to the picture */
            $format = array('R' => 255, 'G' => 255, 'B' => 255);
            $chart_canvas->drawRectangle(1, 1, 698, $height - 2, $format);
            
            /* Set the default font properties */
            $chart_canvas->setFontProperties(
                array(
                    'FontName' => Path :: getInstance()->getVendorPath() .
                         'szymach/c-pchart/src/Resources/fonts/verdana.ttf', 
                        'FontSize' => 8, 
                        'R' => 0, 
                        'G' => 0, 
                        'B' => 0));
            
            /* Create the pRadar object */
            $radar_chart = new pRadar();
            
            /* Draw a polar chart */
            $chart_canvas->setGraphArea($graph_area_left, 20, 579, $height - 21);
            $options = array(
                'BackgroundGradient' => array(
                    'StartR' => 255, 
                    'StartG' => 255, 
                    'StartB' => 255, 
                    'StartAlpha' => 100, 
                    'EndR' => 172, 
                    'EndG' => 214, 
                    'EndB' => 239, 
                    'EndAlpha' => 50), 
                'FontSize' => 6);
            $radar_chart->drawRadar($chart_canvas, $chart_data, $options);
            
            if ($number_of_rows > 1)
            {
                $chart_canvas->drawLegend(
                    20, 
                    26, 
                    array(
                        'Style' => LEGEND_BOX, 
                        'Mode' => LEGEND_VERTICAL, 
                        'R' => 250, 
                        'G' => 250, 
                        'B' => 250, 
                        'Margin' => 5));
            }
            
            /* Render the picture */
            $chart_canvas->render($path);
        }
        
        return $this->getUrl($md5);
    }
}
