<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Glossary;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass\Glossary;

/**
 * $Id: glossary_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * 
 * @package application.lib.weblcms.tool.glossary
 */

/**
 * This tool allows a user to publish glossarys in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{

    public static function get_allowed_types()
    {
        return array(Glossary::class_name());
    }

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;
        return $browser_types;
    }
}
