<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Evaluation\Storage\DataClass\Evaluation;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;

/**
 *
 * @package application.lib.weblcms.tool.evaluation.component
 */

/**
 * This tool allows a user to publish evaluations in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable,
    IntroductionTextSupportInterface
{

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;

        return $browser_types;
    }

    public static function get_allowed_types()
    {
        return array(Evaluation::class_name());
    }
}
