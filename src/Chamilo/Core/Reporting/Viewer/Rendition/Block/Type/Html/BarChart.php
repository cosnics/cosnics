<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;

use Chamilo\Core\Reporting\Viewer\Chart\pChamiloImage;
use Chamilo\Libraries\File\Path;
use CpChart\Classes\pData;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class BarChart extends Chart
{

    public function get_path()
    {
        $reporting_data = $this->get_block()->retrieve_data();
        
        if ($reporting_data->is_empty())
        {
            return false;
        }
        
        $md5 = md5(serialize(array('bar_chart', $reporting_data)));
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
                if ($number_of_bars <= 7)
                {
                    $width = 600;
                    $graph_area_left = 160;
                }
                else
                {
                    $width = 282 + (48 * $number_of_bars) + (25 * $number_of_categories) + 20;
                    $graph_area_left = 160;
                }
            }
            else
            {
                if ($number_of_bars <= 9)
                {
                    $width = 600;
                    $graph_area_left = 70;
                }
                else
                {
                    $width = 122 + (48 * $number_of_bars) + (25 * $number_of_categories) + 20;
                    $graph_area_left = 70;
                }
            }
            
            if ($number_of_rows <= 25)
            {
                $height = 370;
                $graph_area_bottom = 280;
            }
            else
            {
                $height = (13 * $number_of_rows) + 40;
                $graph_area_bottom = $height - 90;
            }
            
            /* Create the pChart object */
            $chart_canvas = new pChamiloImage($width, $height, $chart_data);
            $chart_canvas->Antialias = false;
            
            /* Draw a solid background */
            $format = array('R' => 240, 'G' => 240, 'B' => 240);
            $chart_canvas->drawFilledRectangle(0, 0, $width - 1, $height - 1, $format);
            
            /* Add a border to the picture */
            $format = array('R' => 255, 'G' => 255, 'B' => 255);
            $chart_canvas->drawRectangle(1, 1, $width - 2, $height - 2, $format);
            
            /* Set the default font properties */
            $chart_canvas->setFontProperties(
                array(
                    'FontName' => Path::getInstance()->getVendorPath() .
                         'szymach/c-pchart/src/Resources/fonts/verdana.ttf', 
                        'FontSize' => 8, 
                        'R' => 0, 
                        'G' => 0, 
                        'B' => 0));
            
            /* Draw the scale */
            $chart_canvas->setGraphArea($graph_area_left, 20, $width - 21, $graph_area_bottom - 1);
            $chart_canvas->drawFilledRectangle(
                $graph_area_left, 
                20, 
                $width - 21, 
                $graph_area_bottom - 1, 
                array('R' => 0, 'G' => 0, 'B' => 0, 'Surrounding' => - 200, 'Alpha' => 3));
            $chart_canvas->drawScale(
                array(
                    'DrawSubTicks' => TRUE, 
                    'LabelRotation' => 315, 
                    'GridR' => 0, 
                    'GridG' => 0, 
                    'GridB' => 0, 
                    'GridAlpha' => 7));
            $chart_canvas->setShadow(TRUE, array('X' => 1, 'Y' => 1, 'R' => 0, 'G' => 0, 'B' => 0, 'Alpha' => 10));
            $chart_canvas->drawBarChart(
                array(
                    'DisplayOrientation' => ORIENTATION_AUTO, 
                    'DisplayValues' => true, 
                    'DisplayColor' => DISPLAY_AUTO, 
                    'Surrounding' => 30, 
                    'DisplayOffset' => 3));
            
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
