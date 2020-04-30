<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Note;

use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Note\Storage\DataClass\Note;

/**
 *
 * @package application.lib.weblcms.tool.note
 */

/**
 * This tool allows a user to publish notes in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{
    const ACTION_VIEW_NOTES = 'Viewer';

    public static function get_allowed_types()
    {
        return array(Note::class);
    }
}
