<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Component;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Geolocation\Manager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.lib.weblcms.tool.geolocation.component
 */
class BrowserComponent extends Manager
{

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_BROWSE_PUBLICATION_TYPE;

        return parent::get_additional_parameters($additionalParameters);
    }

    public function show_additional_information($browser)
    {
        $publications = $browser->get_publications();

        if (count($publications) > 0)
        {
            $html = [];

            $html[] = '<br /><br /><h3>' . Translation::get('LocationsSummary') . '</h3>';
            $html[] = '<script src="http://maps.google.com/maps/api/js?sensor=false"></script>';
            $html[] = ResourceManager::getInstance()->getResourceHtml(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository\ContentObject\PhysicalLocation', true) .
                'Plugin\GoogleMaps.js'
            );
            $html[] = '<div id="map_canvas" style="border: 1px solid black; height:500px"></div>';
            $html[] = '<script>';
            $html[] = 'initialize(8);';

            foreach ($publications as $publication)
            {
                $publication_object = DataClass::factory(ContentObjectPublication::class, $publication);

                if ($publication_object->is_visible_for_target_users())
                {
                    $html[] = 'codeAddress(\'' . $publication_object->get_content_object()->get_location() . '\', \'' .
                        $publication_object->get_content_object()->get_title() . '\');';
                }
            }

            $html[] = '</script>';

            return implode(PHP_EOL, $html);
        }
    }
}
