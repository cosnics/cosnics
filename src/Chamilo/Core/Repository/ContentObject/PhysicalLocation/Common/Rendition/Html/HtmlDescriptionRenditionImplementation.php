<?php
namespace Chamilo\Core\Repository\ContentObject\PhysicalLocation\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\PhysicalLocation\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition :: launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        $replace = array();

        $replace[] = '<div class="content_object">';
        $replace[] = '<div class="title">';
        $replace[] = $object->get_location();
        $replace[] = '</div>';
        $replace[] = '<div class="description">';
        $replace[] = $this->get_javascript($object);
        $replace[] = '</div>';
        $replace[] = '</div>';

        return implode(PHP_EOL, $replace);
    }

    public function get_javascript($object)
    {
        $html = array();

        $html[] = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\PhysicalLocation', true) .
                 'Plugin/GoogleMaps.js');
        $html[] = '<div id="map_canvas" style="width:100%; border: 1px solid black; height:500px"></div>';
        $html[] = '<script type="text/javascript">';
        $html[] = 'initialize(12);';
        $html[] = 'codeAddress(\'' . $object->get_location() . '\', \'' . $object->get_title() . '\');';
        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }
}
