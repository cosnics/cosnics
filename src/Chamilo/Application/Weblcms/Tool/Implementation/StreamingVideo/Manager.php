<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\StreamingVideo;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;

/**
 *
 * @package application.lib.weblcms.tool.announcement.component
 */

/**
 * This tool allows a user to publish announcements in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable,
    IntroductionTextSupportInterface
{

    public static function get_allowed_types()
    {
        $allowedTypesString = array(
            'Chamilo\Core\Repository\ContentObject\Office365Video\Storage\DataClass\Office365Video',
            'Chamilo\Core\Repository\ContentObject\Vimeo\Storage\DataClass\Vimeo',
            'Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass\Youtube',
            'Hogent\Core\Repository\ContentObject\Mediamosa\Storage\DataClass\Mediamosa',
            'Hogent\Core\Repository\ContentObject\Video\Storage\DataClass\Video');

        $allowedTypes = [];

        foreach ($allowedTypesString as $allowedTypeString)
        {
            if (class_exists($allowedTypeString))
            {
                $allowedTypes[] = $allowedTypeString;
            }
        }

        return $allowedTypes;
    }

    public function get_available_browser_types()
    {
        $browser_types = [];
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_GALLERY;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_SLIDESHOW;
        return $browser_types;
    }
}
