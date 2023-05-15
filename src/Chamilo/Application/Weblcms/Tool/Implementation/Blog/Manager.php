<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Blog;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Blog\Storage\DataClass\Blog;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;

/**
 * @package application.lib.weblcms.tool.blog
 */

/**
 * This tool allows a user to publish learning paths in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
    implements Categorizable, IntroductionTextSupportInterface
{
    public const CONTEXT = __NAMESPACE__;

    public static function get_allowed_types()
    {
        return [Blog::class];
    }

    public function get_available_browser_types()
    {
        $browser_types = [];
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;

        return $browser_types;
    }
}
