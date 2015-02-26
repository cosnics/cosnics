<?php
namespace Chamilo\Core\Repository\ContentObject\HotspotQuestion\Implementation\Rendition\Html;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\HotspotQuestion\Implementation\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

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
        return ContentObjectRendition :: launch($this);
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
            $html[] = '<div id="hotspot_container"><div id="hotspot_image" style="width: ' . $dimensions[0] .
                 'px; height: ' . $dimensions[1] . 'px; background-image: url(' . $image->get_url() . ')"></div></div>';
            
            // $html[] = '<img class="hotspot_image_display" src="' . $image->get_url() . '" alt="' .
            // $image->get_title() . '" title="' . $image->get_title() . '" />';
        }
        else
        {
            $html[] = '<div id="hotspot_container"><div id="hotspot_image"></div></div>';
        }
        $html[] = '';
        
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="checkbox"></th>';
        $html[] = '<th>' . Translation :: get('HotspotTableTitle') . '</th>';
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
        
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getPluginPath('Chamilo\Configuration', true) . 'jquery/jquery.draw.js');
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getPluginPath('Chamilo\Configuration', true) . 'jquery/phpjs.js');
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getBasePath(true) .
                 'repository/content_object/hotspot_question/resources/javascript/rendition.js');
        
        return implode("\n", $html);
    }
}
