<?php
namespace Chamilo\Application\Weblcms\Admin\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 *
 * @package Chamilo\Application\Weblcms\Admin\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    const ACTION_COURSE_CATEGORY_FEED = 'CourseCategoryFeed';
    const ACTION_COURSE_FEED = 'CourseFeed';
}
