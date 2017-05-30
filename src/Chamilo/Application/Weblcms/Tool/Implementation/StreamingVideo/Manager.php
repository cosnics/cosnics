<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\StreamingVideo;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Matterhorn\Storage\DataClass\Matterhorn;
use Chamilo\Core\Repository\ContentObject\Office365Video\Storage\DataClass\Office365Video;
use Chamilo\Core\Repository\ContentObject\Vimeo\Storage\DataClass\Vimeo;
use Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass\Youtube;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;

/**
 * $Id: announcement_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
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
        $allowedTypes = array(
            Youtube::class_name(), 
            Vimeo::class_name(), 
            Matterhorn::class_name(), 
            Office365Video::class_name());
        
        $hogentTypes = array(
            'Hogent\Core\Repository\ContentObject\Mediamosa\Storage\DataClass\Mediamosa',
            'Hogent\Core\Repository\ContentObject\Video\Storage\DataClass\Video'
        );
        
        foreach ($hogentTypes as $hogentType)
        {
            if (class_exists($hogentType))
            {
                $allowedTypes[] = $hogentType;
            }
        }
        
        return $allowedTypes;
    }

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_GALLERY;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_SLIDESHOW;
        return $browser_types;
    }
}
