<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\FrequentlyAskedQuestions;

use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Storage\DataClass\FrequentlyAskedQuestions;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;

/**
 * $Id: forum_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.forum
 */

/**
 * This tool allows a user to publish forums in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable, IntroductionTextSupportInterface
{
    const ACTION_MANAGE_CATEGORIES = 'CategoryManager';
    const PARAM_SUBSCRIBE_ID = 'subscribe';
    const PARAM_FAQ_ID = 'faq_id';

    public static function get_allowed_types()
    {
        return array(FrequentlyAskedQuestions :: class_name());
    }
}
