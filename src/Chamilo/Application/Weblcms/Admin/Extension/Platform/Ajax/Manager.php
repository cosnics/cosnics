<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Application\Weblcms\Admin\Extension\Platform\Ajax
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    public const ACTION_COURSE_CATEGORY_FEED = 'CourseCategoryFeed';
    public const ACTION_COURSE_FEED = 'CourseFeed';

    public const CONTEXT = __NAMESPACE__;
}
