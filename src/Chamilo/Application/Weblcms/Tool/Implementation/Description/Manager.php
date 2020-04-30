<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Description;

use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass\Description;

/**
 *
 * @package application.lib.weblcms.tool.description
 */

/**
 * This tool allows a user to publish descriptions in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{
    const ACTION_VIEW_DESCRIPTIONS = 'Viewer';

    public static function get_allowed_types()
    {
        return array(Description::class /*
                                                * , File :: class_name(), Page :: class_name(), Webpage :: class_name()
                                                */
);
    }
}
