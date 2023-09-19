<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Link;

use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link;
use Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed;
use Chamilo\Libraries\Architecture\Interfaces\CategorizableInterface;

/**
 * @package application.lib.weblcms.tool.link
 */

/**
 * This tool allows a user to publish links in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
    implements CategorizableInterface, IntroductionTextSupportInterface
{
    public const CONTEXT = __NAMESPACE__;

    public const TOOL_NAME = 'Link';

    public static function get_allowed_types()
    {
        return [Link::class, RssFeed::class];
    }
}
