<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Component\GeolocationBrowser;

use Chamilo\Application\Weblcms\Renderer\PublicationList\Type\ContentObjectPublicationDetailsRenderer;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

/**
 *
 * @package application.lib.weblcms.tool.geolocation.component.geolocation_browser
 */
class GeolocationDetailsRenderer extends ContentObjectPublicationDetailsRenderer
{

    public function __construct($browser)
    {
        parent::__construct($browser);
    }

    public function render_description($publication)
    {
        $lo = $publication->get_content_object();

        $html = array();

        $html[] = $lo->get_description();

        $html[] = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\PhysicalLocation', true) .
                 'Plugin\GoogleMaps.js');
        $html[] = '<div id="map_canvas" style="border: 1px solid black; height:500px"></div>';
        $html[] = '<script type="text/javascript">';
        $html[] = 'initialize(12);';
        $html[] = 'codeAddress(\'' . $lo->get_location() . '\', \'' . $lo->get_title() . '\');';
        $html[] = '</script>';

        return implode(PHP_EOL, $html);
    }
}
