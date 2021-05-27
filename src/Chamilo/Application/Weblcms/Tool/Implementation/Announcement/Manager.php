<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Announcement;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Announcement\Storage\DataClass\Announcement;
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

    public function get_available_browser_types()
    {
        $browser_types = [];
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;

        return $browser_types;
    }

    public static function get_allowed_types()
    {
        return array(Announcement::class);
    }
}
