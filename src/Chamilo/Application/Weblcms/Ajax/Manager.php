<?php
namespace Chamilo\Application\Weblcms\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 *
 * @package Chamilo\Application\Weblcms\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    const ACTION_XML_GROUP_MENU_FEED = 'XmlPublicationsTreeFeed';
    const ACTION_XML_COURSE_USER_GROUP_FEED = 'XmlCourseUserGroupFeed';
    const ACTION_SAVE_HOTPOTATOES_SCORE = 'HotpotatoesSaveScore';
    const ACTION_SAVE_LEARNING_PATH_HOTPOTATOES_SCORE = 'LpHotpotatoesSaveScore';
}
