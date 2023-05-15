<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Geolocation;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\PhysicalLocation\Storage\DataClass\PhysicalLocation;

/**
 * @package application.lib.weblcms.tool.geolocation
 */

/**
 * This tool allows a user to publish geolocations in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{
    public const CONTEXT = __NAMESPACE__;

    public static function get_allowed_types()
    {
        return [PhysicalLocation::class];
    }

    public function get_available_browser_types()
    {
        return [ContentObjectPublicationListRenderer::TYPE_TABLE];
    }
}
