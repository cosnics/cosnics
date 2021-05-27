<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Wiki;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Wiki\Storage\DataClass\Wiki;

/**
 *
 * @package application.lib.weblcms.tool.wiki
 */

/**
 * This tool allows a user to publish wikis in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{

    public function get_available_browser_types()
    {
        $browser_types = [];
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;
        return $browser_types;
    }

    public static function get_allowed_types()
    {
        return array(Wiki::class);
    }
}
