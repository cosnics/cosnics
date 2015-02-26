<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * $Id: geolocation_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.geolocation.component
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    public function show_additional_information($browser)
    {
        $publications = $browser->get_publications();

        if (count($publications) > 0)
        {
            $html = array();

            $html[] = '<br /><br /><h3>' . Translation :: get('LocationsSummary') . '</h3>';
            $html[] = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
            $html[] = ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getBasePath(true) . 'Configuration/Resources/Javascript/GoogleMaps.js');
            $html[] = '<div id="map_canvas" style="border: 1px solid black; height:500px"></div>';
            $html[] = '<script type="text/javascript">';
            $html[] = 'initialize(8);';

            foreach ($publications as $publication)
            {
                $publication_object = DataClass :: factory(ContentObjectPublication :: class_name(), $publication);

                if ($publication_object->is_visible_for_target_users())
                {
                    $html[] = 'codeAddress(\'' . $publication_object->get_content_object()->get_location() . '\', \'' .
                         $publication_object->get_content_object()->get_title() . '\');';
                }
            }

            $html[] = '</script>';

            return implode("\n", $html);
        }
    }

    public function get_additional_parameters()
    {
        return array(self :: PARAM_BROWSE_PUBLICATION_TYPE);
    }
}
