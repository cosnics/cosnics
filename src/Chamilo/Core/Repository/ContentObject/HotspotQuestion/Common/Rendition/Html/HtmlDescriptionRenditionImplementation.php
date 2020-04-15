<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    private $colours = array(
        '#ff0000', 
        '#f2ef00', 
        '#00ff00', 
        '#00ffff', 
        '#0000ff', 
        '#ff00ff', 
        '#0080ff', 
        '#ff0080', 
        '#00ff80', 
        '#ff8000', 
        '#8000ff');

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $html = array();
        
        $content_object = $this->get_content_object();
        $options = $content_object->get_answers();
        $image = $content_object->get_image_object();
        
        if (! is_null($image))
        {
            $dimensions = getimagesize($image->get_full_path());
            $scaledDimensions = Utilities::scaleDimensions(
                600, 
                450, 
                array('width' => $dimensions[0], 'height' => $dimensions[1]));
            
            $html[] = '<div id="hotspot_container"><div id="hotspot_image" style="width: ' .
                 $scaledDimensions['thumbnailWidth'] . 'px; height: ' . $scaledDimensions['thumbnailHeight'] .
                 'px; background-size: ' . $scaledDimensions['thumbnailWidth'] . 'px ' .
                 $scaledDimensions['thumbnailHeight'] . 'px;background-image: url(' . \Chamilo\Core\Repository\Manager::get_document_downloader_url(
                    $image->get_id(), 
                    $image->calculate_security_code()) . ')"></div></div>';
        }
        else
        {
            $html[] = '<div id="hotspot_container"><div id="hotspot_image"></div></div>';
        }
        $html[] = '';
        
        $html[] = '<table class="table table-striped table-bordered table-hover table-data">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th></th>';
        $html[] = '<th>' . Translation::get('HotspotTableTitle') . '</th>';
        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';
        foreach ($options as $index => $option)
        {
            $html[] = '<tr class="' . ($index % 2 == 0 ? 'row_even' : 'row_odd') . '">';
            $html[] = '<td><div class="colour_box" style="background-color: ' . $this->colours[$index] .
                 ';"></div><div id="coordinates_' . $index . '" class="coordinates" style="display: none;">' .
                 $option->get_hotspot_coordinates() . '</div></td>';
            
            $renderer = new ContentObjectResourceRenderer($this->get_context(), $option->get_answer());
            $html[] = '<td>' . $renderer->run() . '</td>';
            
            $html[] = '</tr>';
        }
        $html[] = '</tbody>';
        $html[] = '</table>';
        
        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getPluginPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion', true) .
                 'jquery.draw.js');
        $html[] = ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\HotspotQuestion', true) .
                 'Rendition.js');
        
        return implode(PHP_EOL, $html);
    }
}
